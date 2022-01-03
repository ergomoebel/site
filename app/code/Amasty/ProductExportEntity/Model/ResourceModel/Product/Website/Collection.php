<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Website;

use Magento\Catalog\Model\Product\Website;
use Magento\Catalog\Model\ResourceModel\Product\Website as WebsiteResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Website::class, WebsiteResource::class);
    }

    /**
     * @inheritdoc
     */
    public function addFieldToSelect($field, $alias = null)
    {
        if (!is_array($field)) {
            if ($field == 'product_sku') {
                return $this->joinProductSku();
            }
        }

        return parent::addFieldToSelect($field, $alias);
    }

    /**
     * Joins product SKU to collection
     *
     * @return $this
     */
    private function joinProductSku()
    {
        if (!$this->getFlag('sku_joined')) {
            $this->getSelect()
                ->joinLeft(
                    ['product_entity_table' => $this->getTable('catalog_product_entity')],
                    'main_table.product_id = product_entity_table.entity_id',
                    [
                        'product_sku' => 'product_entity_table.sku'
                    ]
                );
            $this->setFlag('sku_joined', true);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'product_sku') {
            $this->addFilter('product_entity_table.sku', $condition, 'public');

            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
