<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Type\Configurable\Link;

use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableResource;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Fields that doesn't required to be added to select
     */
    const PRE_SELECTED_FIELDS = ['sku'];

    protected function _construct()
    {
        $this->_init(DataObject::class, ConfigurableResource::class);
    }

    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()])
            ->joinInner(
                ['product_entity' => $this->getTable('catalog_product_entity')],
                'product_entity.entity_id = main_table.product_id',
                ['sku' => 'product_entity.sku']
            );

        $this->addFilterToMap(
            'sku',
            'product_entity.sku'
        );
        return $this;
    }

    public function addFieldToSelect($field, $alias = null)
    {
        if (is_array($field)) {
            $preSelectedFields = array_intersect($field, self::PRE_SELECTED_FIELDS);
            if (count($preSelectedFields)) {
                $field = array_diff($field, $preSelectedFields);
            }
        } elseif (in_array($field, self::PRE_SELECTED_FIELDS)) {
            return $this;
        }

        return parent::addFieldToSelect($field, $alias);
    }
}
