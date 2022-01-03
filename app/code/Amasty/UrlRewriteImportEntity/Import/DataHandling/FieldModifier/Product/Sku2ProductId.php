<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteImportEntity
 */


declare(strict_types=1);

namespace Amasty\UrlRewriteImportEntity\Import\DataHandling\FieldModifier\Product;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class Sku2ProductId implements FieldModifierInterface
{
    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var array
     */
    private $skuToEntityIdMap = [];

    public function __construct(ProductResource $productResource)
    {
        $this->productResource = $productResource;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        $productId = $this->getIdBySku($value);
        if ($productId) {
            return $productId;
        }

        return null;
    }

    /**
     * Get product entity Id by sku
     *
     * @param string $sku
     * @return int|false
     */
    private function getIdBySku($sku)
    {
        if (!isset($this->skuToEntityIdMap[$sku])) {
            $this->skuToEntityIdMap[$sku] = $this->productResource->getIdBySku($sku);
        }

        return $this->skuToEntityIdMap[$sku];
    }
}
