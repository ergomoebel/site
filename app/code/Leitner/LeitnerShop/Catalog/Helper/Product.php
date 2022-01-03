<?php
namespace Leitner\LeitnerShop\Catalog\Helper;


class Product extends \Magento\Catalog\Helper\Product
{

    /**
     * Inits product to be used for product controller actions and layouts (with param to override visibility)
     * $params can have following data:
     *   'category_id' - id of category to check and append to product as current.
     *     If empty (except FALSE) - will be guessed (e.g. from last visited) to load as current.
     *
     * @param int $productId
     * @param \Magento\Framework\App\Action\Action $controller
     * @param \Magento\Framework\DataObject $params
     *
     * @return bool|ModelProduct
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function initProduct($productId, $controller, $params = null)
    {
        // Prepare data for routine
        if (!$params) {
            $params = new \Magento\Framework\DataObject();
        }

        // Init and load product
        $this->_eventManager->dispatch(
            'catalog_controller_product_init_before',
            ['controller_action' => $controller, 'params' => $params]
        );

        if (!$productId) {
            return false;
        }

        try {
            $product = $this->productRepository->getById($productId, false, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
/*
        if (!$this->canShow($product)) {
            return false;
        }
        if (!in_array($this->_storeManager->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
            return false;
        }
*/

        // [visibility override BEGIN]
        if (!$params->getOverrideVisibility()) {
            if (!$this->canShow($product)) {
                return false;
            }
        } else {
            if (!$product->isVisibleInCatalog()) {
                return false;
            }
        }
        // [visibility END]
        
        // Load product current category
        $categoryId = $params->getCategoryId();
        if (!$categoryId && $categoryId !== false) {
            $lastId = $this->_catalogSession->getLastVisitedCategoryId();
            if ($product->canBeShowInCategory($lastId)) {
                $categoryId = $lastId;
            }
        } elseif (!$product->canBeShowInCategory($categoryId)) {
            $categoryId = null;
        }

        if ($categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
            } catch (NoSuchEntityException $e) {
                $category = null;
            }
            if ($category) {
                $product->setCategory($category);
                $this->_coreRegistry->register('current_category', $category);
            }
        }

        // Register current data and dispatch final events
        $this->_coreRegistry->register('current_product', $product);
        $this->_coreRegistry->register('product', $product);

        try {
            $this->_eventManager->dispatch(
                'catalog_controller_product_init_after',
                ['product' => $product, 'controller_action' => $controller]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);
            return false;
        }

        return $product;
    }

    /**
     * Prepares product options by buyRequest: retrieves values and assigns them as default.
     * Also parses and adds product management related values - e.g. qty
     *
     * @param ModelProduct $product
     * @param \Magento\Framework\DataObject $buyRequest
     * @return Product
     */
    public function prepareProductOptions($product, $buyRequest)
    {
        $optionValues = $product->processBuyRequest($buyRequest);
        //$this->_logger->debug('processed buyrequest');
        //$this->_logger->debug(print_r($optionValues,TRUE));
        $optionValues->setQty($buyRequest->getQty());
        $product->setPreconfiguredValues($optionValues);
        /*if($product->hasPreconfiguredValues()) {
          $this->_logger->debug('preconfigured values found');
        } else {
          $this->_logger->debug('no preconfigured values found');
        }*/
        return $this;
    }

}
