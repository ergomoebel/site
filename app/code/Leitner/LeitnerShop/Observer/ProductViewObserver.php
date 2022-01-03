<?php
namespace Leitner\LeitnerShop\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductViewObserver implements ObserverInterface
{
  
    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $productViewHelper;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Helper\Product\View  $productViewHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(\Psr\Log\LoggerInterface $logger, \Magento\Catalog\Helper\Product\View $productViewHelper, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Catalog\Model\Session $catalogSession)
    {
        $this->logger = $logger;
        $this->productViewHelper = $productViewHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->catalogSession = $catalogSession;
    }
  
    /**
     * Product view observer 
     *
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //$myEventData = $observer->getData('myEventData');
        //$this->logger->debug(print_r(array_keys($observer->getData()),TRUE));
        // Additional observer execution code...
        return $this->forwardToConfigurable($observer);
    }
    
    /**
     * Generates config array to reflect the simple product's ($currentProduct)
     * configuration in its parent configurable product
     *
     * @param \Magento\Catalog\Model\Product $parentProduct
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @return array array( configoptionid -> value )
     */
    protected function generateConfigData(\Magento\Catalog\Model\Product $parentProduct, \Magento\Catalog\Model\Product $currentProduct)
    {
        /* @var $typeInstance Magento\ConfigurableProduct\Model\Product\Type\Configurable */
        $typeInstance = $parentProduct->getTypeInstance();
        if (!$typeInstance instanceof \Magento\ConfigurableProduct\Model\Product\Type\Configurable) {
            return; // not a configurable product
        }
        $configData = array();
        $attributes = $typeInstance->getUsedProductAttributes($parentProduct);
  
        foreach ($attributes as $code => $data) {
            $configData[$code] = $currentProduct->getData($data->getAttributeCode());
        }
  
        return $configData;
    }
    
    /**
     * Checks if the current product has a super-product assigned
     * Finds the super product
     * @param $observer Varien_Event_Observer $observer
     * @throws Exception
     */
    public function forwardToConfigurable($observer)
    {
        $controller = $observer->getControllerAction();
        $productId = (int)$controller->getRequest()->getParam('id');
        $categoryId = (int)$controller->getRequest()->getParam('category', false);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$helperFactory = $objectManager->get('\Magento\Core\Model\Factory\Helper');
        

        $parentIds = $objectManager->create('\Magento\ConfigurableProduct\Model\Product\Type\Configurable')->getParentIdsByChild($productId);
        
        if (empty($parentIds)) { // does not have a parent -> nothing to do
            $this->logger->debug(sprintf('Found no parent for product id %d', $productId));
            return;
        }

        while (count($parentIds) > 0) {
            $parentId = array_shift($parentIds);
            /* @var $parentProduct Magento\Catalog\Model\Product */
            $parentProduct = $objectManager->create('\Magento\Catalog\Model\Product');
            $parentProduct->load($parentId);
            if (!$parentProduct->getId()) {
                throw new Exception(sprintf('Can not load parent product with ID %d', $parentId));
            }

            if ($parentProduct->isVisibleInCatalog()) {
                break;
            }
            // try to find other products if one parent product is not visible -> loop
        }

        if (!$parentProduct->isVisibleInCatalog()) {
            //Mage::log(sprintf('Not enabled parent for product id %d found.', $productId), \Zend_Log::WARN);
            $this->logger->debug(sprintf('Not enabled parent for product id %d found.', $productId));
            return;
        }

        if (!empty($parentIds)) {
            $this->logger->debug(sprintf('Product with id %d has more than one enabled parent. Choosing first.', $productId));
            //Mage::log(sprintf('Product with id %d has more than one enabled parent. Choosing first.', $productId), \Zend_Log::NOTICE);
            
        }
        //$this->logger->debug('product id: ' . $productId);
        //$this->logger->debug('parent id: ' . $parentId);


            /* @var $currentProduct Magento\Catalog\Model\Product */
        $currentProduct = $objectManager->create('\Magento\Catalog\Model\Product');
        $currentProduct->load($productId);

        $params = $objectManager->get('\Magento\Framework\DataObject');
        $params->setCategoryId($categoryId);
        $params->setConfigureMode(true);
        $buyRequest = $objectManager->get('\Magento\Framework\DataObject');
        $buyRequest->setSuperAttribute($this->generateConfigData($parentProduct, $currentProduct)); // example format: array(525 => "99"));
        $params->setBuyRequest($buyRequest);
        $this->logger->debug(print_r($this->generateConfigData($parentProduct, $currentProduct),TRUE));
        
        //$this->catalogSession->setSuperAttributes($this->generateConfigData($parentProduct, $currentProduct));

        // override visibility setting of configurable product
        // in case only simple products should be visible in the catalog
        // TODO: make this behaviour configurable
        $params->setOverrideVisibility(true);


        $controller->getRequest()->setDispatched(true);
        // avoid double dispatching
        // @see Mage_Core_Controller_Varien_Action::dispatch()
        //$controller->setFlag('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
        $controller->getActionFlag()->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);

        $resultPage = $this->resultPageFactory->create();

        //$this->logger->debug('result page: '.get_class($resultPage));
        //$this->logger->debug('response class: '.get_class($controller->getResponse()));
        $this->productViewHelper->prepareAndRender($resultPage, $parentId, $controller, $params);
        //$controller->setResponse($resultPage);
        $controller->setResponse($resultPage);
        
    }
  
}