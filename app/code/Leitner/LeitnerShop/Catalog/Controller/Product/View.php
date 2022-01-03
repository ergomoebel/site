<?php
namespace Leitner\LeitnerShop\Catalog\Controller\Product;

class View extends \Magento\Catalog\Controller\Product\View
{
    protected $http;

    protected $productHelper;

    protected $configurable;

    public function orig__construct(
        \Magento\Framework\App\Response\Http $http,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    ) {
        $this->http = $http;
        $this->productHelper =$productHelper;
        $this->configurable = $configurable;
    }

    public function aroundExecute__(
        \Magento\Catalog\Controller\Product\View $subject,
        \Closure $proceed
    )
    {
        $productId = (int) $subject->getRequest()->getParam('id');
        $parentIds = $this->configurable->getParentIdsByChild($productId);
        $parentId = array_shift($parentIds);

        if($parentId) {
            $categoryId = (int)$subject->getRequest()->getParam('category', false);
            $productId = (int)$parentId;

            $params = new \Magento\Framework\DataObject();
            $params->setCategoryId($categoryId);

            /** @var \Magento\Catalog\Helper\Product $product */
            $product = $this->productHelper->initProduct($productId, $subject, $params);;
            $this->http->setRedirect($product->getProductUrl(), 301);
        }
        return $proceed();
    }
    
    public function setResponse($response) {
      $this->_response = $response;
    }
}