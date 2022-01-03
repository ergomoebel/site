<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Category;

use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\Data\Collection\AbstractDb;

class Collection extends CategoryCollection
{
    const NON_ATTRIBUTE_FIELDS = ['created_in', 'updated_in'];
    const PRODUCT_ENTITY_FIELDS = ['product_sku' => 'product_entity_table.sku'];

    /**
     * @inheritdoc
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        if (!is_array($attribute)) {
            if ($this->isProductEntityField($attribute)) {
                return $this->joinProductEntityTable();
            }
        }

        return parent::addAttributeToSelect($attribute, $joinType);
    }

    /**
     * Join catalog product entity table
     *
     * @return $this
     */
    private function joinProductEntityTable()
    {
        if (!$this->getFlag('product_entity_table_joined')) {
            $this->getSelect()->joinLeft(
                ['category_product_table' => $this->getTable('catalog_category_product')],
                'category_product_table.category_id = e.entity_id',
                []
            )->joinLeft(
                ['product_entity_table' => $this->getTable('catalog_product_entity')],
                'product_entity_table.entity_id = category_product_table.product_id',
                self::PRODUCT_ENTITY_FIELDS
            );
            $this->setFlag('product_entity_table_joined', true);
        }

        return $this;
    }

    /**
     * Checks if specified field name is of catalog_product_entity table
     *
     * @param string $fieldName
     * @return bool
     */
    private function isProductEntityField($fieldName): bool
    {
        $productEntityFields = array_keys(self::PRODUCT_ENTITY_FIELDS);

        return in_array($fieldName, $productEntityFields);
    }

    /**
     * Wrapper for compatibility with \Magento\Framework\Data\Collection\AbstractDb
     * Fixed filtering by the field "created_in" and "updated_in"
     *
     * @param mixed $attribute
     * @param mixed $condition
     * @return Collection|AbstractDb
     * @codeCoverageIgnore
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        if (in_array($attribute, self::NON_ATTRIBUTE_FIELDS)) {
            return AbstractDb::addFieldToFilter($attribute, $condition);
        } else {
            return parent::addFieldToFilter($attribute, $condition);
        }
    }
}
