<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class SkuToProductId
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
     * Converts sku into product_id
     *
     * @param string $sku
     * @return false|int
     */
    public function executeValue($sku)
    {
        return $this->getIdBySku($sku);
    }

    /**
     * Retrieves product_id by sku and update corresponding product_id data column
     *
     * @param array $row
     * @param string $productIdKey
     * @param string $skuKey
     * @return array
     */
    public function executeRow(array &$row, $productIdKey = 'product_id', $skuKey = 'sku'): array
    {
        if (isset($row[$skuKey])) {
            $sku = trim($row[$skuKey]);
            if (empty($sku)) {
                return $row;
            }

            $productId = $this->getIdBySku($sku);
            if ($productId) {
                $row[$productIdKey] = $productId;
            }
        }

        return $row;
    }

    /**
     * Apply executeRows() method for multiple rows
     *
     * @param array $rows
     * @param string $productIdKey
     * @param string $skuKey
     * @return array
     */
    public function executeRows(array &$rows, $productIdKey = 'product_id', $skuKey = 'sku'): array
    {
        foreach ($rows as &$row) {
            $this->executeRow($row, $productIdKey, $skuKey);
        }

        return $rows;
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
