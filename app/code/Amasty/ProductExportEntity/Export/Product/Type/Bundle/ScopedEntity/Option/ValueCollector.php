<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\Product\Type\Bundle\ScopedEntity\Option;

use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\AbstractCollector;
use Amasty\ProductExportEntity\Model\ResourceModel\Product\Type\Bundle\Option\Value\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;

class ValueCollector extends AbstractCollector
{
    /**
     * Fields that doesn't required to be added to select
     */
    const PRE_SELECTED_FIELDS = ['title'];

    protected function performCollect(AbstractDb $collection, array $defaultItems, $scopeValue)
    {
        $items = [];
        /** @var DataObject $label */
        foreach ($collection as $label) {
            $items[] = $this->prepareEntityData(
                $label->getData(),
                $defaultItems,
                'value_id',
                self::STORE_ID_FIELD,
                $scopeValue
            );
        }
        return $items;
    }

    /**
     * Prepare label collection
     *
     * @param Collection|AbstractDb $collection
     * @param int $scopeValue
     * @return Collection|AbstractDb
     */
    protected function prepareCollection(AbstractDb $collection, $scopeValue)
    {
        $collection->addOrder('value_id', Collection::SORT_ORDER_ASC)
            ->setStoreId($scopeValue);

        $this->addFieldsToSelect($collection, $this->fieldsToSelect);
        foreach ($this->fieldsToFilter as $field) {
            $collection->addFieldToFilter(...$field);
        }

        return $collection;
    }

    /**
     * Add fields to select
     *
     * @param Collection $collection
     * @param array $fields
     */
    private function addFieldsToSelect(Collection $collection, array $fields)
    {
        foreach ($fields as $field) {
            $fieldNames = $this->fieldArgumentProcessor->getFieldNames($field);
            $preSelectedFields = array_intersect($fieldNames, self::PRE_SELECTED_FIELDS);
            if (count($preSelectedFields)) {
                $fieldsToAdd = array_diff($fieldNames, $preSelectedFields);
                foreach ($fieldsToAdd as $fieldName) {
                    $collection->addFieldToSelect($fieldName);
                }
            } else {
                $collection->addFieldToSelect(...$field);
            }
        }
    }
}
