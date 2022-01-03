<?php
/**
 * 
 */
namespace Leitner\LeitnerShop\Swatches\Block\Product\Renderer;


class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable {
  /**
     * Get Key for caching block content
     *
     * @return string
     * @since 100.1.0
     */
    public function getCacheKey()
    {
        $currentProduct = $this->getProduct();
        $options = $this->helper->getOptions($currentProduct, $this->getAllowProducts());
        $attributesData = $this->configurableAttributeData->getAttributesData($currentProduct, $options);
        return parent::getCacheKey() . '-' . $this->getProduct()->getId() . $currentProduct->hasPreconfiguredValues() ? ('-' . md5(implode(',', $attributesData['defaultValues']))) :'';
    }

    /**
     * Get block cache life time
     *
     * @return int
     * @since 100.1.0
     */
    protected function getCacheLifetime()
    {
        return parent::hasCacheLifetime() ? parent::getCacheLifetime() : 3600;
    }
  
}
