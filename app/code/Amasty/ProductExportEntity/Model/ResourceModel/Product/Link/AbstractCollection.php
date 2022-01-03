<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Link;

use Magento\Catalog\Model\ResourceModel\Product\Link\Collection;

/**
 * Abstract product link collection
 */
abstract class AbstractCollection extends Collection
{
    const PRODUCT_ENTITY_FIELDS = ['sku'];

    /**
     * @var array
     */
    private $linkAttributes;

    /**
     * Get link type Id
     *
     * @return int
     */
    abstract protected function getLinkTypeId();

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->where('main_table.link_type_id = ?', $this->getLinkTypeId());

        $this->addFilterToMap(
            'link_id',
            'main_table.link_id'
        )->addFilterToMap(
            'link_type_id',
            'main_table.link_type_id'
        );

        return $this;
    }

    public function addFieldToSelect($field, $alias = null)
    {
        if (!is_array($field)) {
            if ($this->isProductEntityField($field)) {
                return $this->joinProductEntityTable();
            } elseif ($this->isProductLinkAttribute($field)) {
                return $this->joinLinkAttribute($field);
            }
        }

        return parent::addFieldToSelect($field, $alias);
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($this->isProductEntityField($field)) {
            $this->addFilter('product_entity_table.' . $field, $condition, 'public');
            return $this;
        }

        if ($this->isProductLinkAttribute($field)) {
            $attribute = $this->getLinkAttributeByCode($field);
            $this->addFilter(
                $this->getLinkAttributeAlias($attribute) . '.value',
                $condition,
                'public'
            );
            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Checks if specified field is of catalog_product_entity table
     *
     * @param string $field
     * @return bool
     */
    private function isProductEntityField($field)
    {
        return in_array($field, self::PRODUCT_ENTITY_FIELDS);
    }

    /**
     * Checks if specified field is link attribute
     *
     * @param string $field
     * @return bool
     */
    private function isProductLinkAttribute($field)
    {
        $linkAttributes = $this->getLinkAttributes();
        return isset($linkAttributes[$field]);
    }

    /**
     * Get link attributes
     *
     * @return array
     */
    private function getLinkAttributes()
    {
        if (!$this->linkAttributes) {
            $this->linkAttributes = [];

            $connection = $this->getConnection();
            $select = $connection->select()->from(
                $this->getTable('catalog_product_link_attribute'),
                [
                    'id' => 'product_link_attribute_id',
                    'code' => 'product_link_attribute_code',
                    'type' => 'data_type'
                ]
            )->where(
                'link_type_id = ?',
                $this->getLinkTypeId()
            );

            $attributes = $connection->fetchAll($select);
            foreach ($attributes as $attributeData) {
                $this->linkAttributes[$attributeData['code']] = $attributeData;
            }
        }
        return $this->linkAttributes;
    }

    /**
     * Get link attribute data by code
     *
     * @param string $attributeCode
     * @return array|null
     */
    private function getLinkAttributeByCode($attributeCode)
    {
        $attributes = $this->getLinkAttributes();
        return $attributes[$attributeCode] ?? null;
    }

    /**
     * Get link attribute table alias
     *
     * @param array $attribute
     * @return string
     */
    private function getLinkAttributeAlias($attribute)
    {
        return sprintf('link_attribute_%s_%s', $attribute['code'], $attribute['type']);
    }

    /**
     * Get link attribute table
     *
     * @param string $attributeType
     * @return string
     */
    private function getLinkAttributeTable($attributeType)
    {
        return $this->getTable('catalog_product_link_attribute_' . $attributeType);
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
                ['product_entity_table' => $this->getTable('catalog_product_entity')],
                'product_entity_table.entity_id = main_table.linked_product_id',
                self::PRODUCT_ENTITY_FIELDS
            );
            $this->setFlag('product_entity_table_joined', true);
        }
        return $this;
    }

    /**
     * Join link attribute values
     *
     * @param string $attributeCode
     * @return $this
     */
    private function joinLinkAttribute($attributeCode)
    {
        if (!$this->getFlag('link_attribute_' . $attributeCode . '_joined')) {
            $connection = $this->getConnection();

            $attribute = $this->getLinkAttributeByCode($attributeCode);
            $attributeType = $attribute['type'];

            $tableAlias = $this->getLinkAttributeAlias($attribute);
            $tableAliasQuoted = $connection->quoteColumnAs($tableAlias, null);

            $this->getSelect()->joinLeft(
                [$tableAlias => $this->getLinkAttributeTable($attributeType)],
                implode(
                    ' AND ',
                    [
                        $tableAliasQuoted . '.link_id = main_table.link_id',
                        $tableAliasQuoted . '.product_link_attribute_id = ' . (int)$attribute['id']
                    ]
                ),
                [$attributeCode => 'value']
            );
            $this->setFlag('link_attribute_' . $attributeCode . '_joined', true);
        }
        return $this;
    }

    protected function _renderFiltersBefore()
    {
        if ($this->hasFilterByAlias('product_entity_table')) {
            $this->joinProductEntityTable();
        }
        foreach ($this->getLinkAttributes() as $attribute) {
            $alias = $this->getLinkAttributeAlias($attribute);
            if ($this->hasFilterByAlias($alias)) {
                $this->joinLinkAttribute($attribute['code']);
            }
        }

        parent::_renderFiltersBefore();
    }

    /**
     * Check if collection has filter by table alias
     *
     * @param string $alias
     * @return bool
     */
    private function hasFilterByAlias($alias)
    {
        foreach ($this->getFilter([]) as $filter) {
            $field = $filter['field'];
            $fieldParts = explode('.', $field);
            if (count($fieldParts) == 2 && $fieldParts[0] == $alias) {
                return true;
            }
        }
        return false;
    }
}
