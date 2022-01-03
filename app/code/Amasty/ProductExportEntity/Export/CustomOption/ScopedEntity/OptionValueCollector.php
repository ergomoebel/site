<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\CustomOption\ScopedEntity;

use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\AbstractCollector;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection;
use Magento\Framework\Data\Collection\AbstractDb;

class OptionValueCollector extends AbstractCollector
{
    const TITLE_FIELDS = ['title', 'store_title'];

    const PRICE_FIELDS = [
        'store_price',
        'store_price_type',
        'price',
        'price_type'
    ];

    /**
     * @var bool
     */
    private $isTitleFieldsAddedToResult = false;

    /**
     * @var bool
     */
    private $isPriceFieldsAddedToResult = false;

    protected function performCollect(AbstractDb $collection, array $defaultItems, $scopeValue)
    {
        $items = [];
        /** @var Value $value */
        foreach ($collection as $value) {
            $itemData = [];
            $valueData = $value->getData();

            foreach ($this->getFieldsToSelectAsArray() as $fieldName) {
                if (isset($valueData[$fieldName])) {
                    if (in_array($fieldName, ['sku', 'sort_order'])) {
                        $itemData[$fieldName] = $valueData[$fieldName];
                    } elseif ($fieldName == self::STORE_ID_FIELD) {
                        $itemData[$fieldName] = $scopeValue;
                    } else {
                        $itemData[$fieldName] = $this->getFieldValue(
                            $fieldName,
                            $defaultItems,
                            $valueData,
                            'option_type_id'
                        );
                    }
                }
            }

            $items[] = $itemData;
        }
        return $items;
    }

    /**
     * Prepare values collection
     *
     * @param Collection|AbstractDb $collection
     * @param int $scopeValue
     * @return AbstractDb
     */
    protected function prepareCollection(AbstractDb $collection, $scopeValue)
    {
        $this->addRequiredFieldsToResult($collection, $scopeValue);
        $collection->setOrder('sort_order', Collection::SORT_ORDER_ASC)
            ->setOrder('title', Collection::SORT_ORDER_ASC);

        $this->addFieldsToSelect(
            $collection,
            $scopeValue,
            $this->fieldsToSelect
        );

        foreach ($this->fieldsToFilter as $field) {
            $collection->addFieldToFilter(...$field);
        }
        return $collection;
    }

    protected function addRequiredFieldsToResult(Collection $collection, $storeId)
    {
        $collection->addFieldToSelect('option_type_id');
        $this->addTitleFieldsToResult($collection, $storeId);
    }

    /**
     * Add fields to select
     *
     * @param Collection $collection
     * @param int $storeId
     * @param array $fields
     */
    private function addFieldsToSelect(Collection $collection, $storeId, array $fields)
    {
        foreach ($fields as $field) {
            $fieldNames = $this->fieldArgumentProcessor->getFieldNames($field);

            foreach (self::TITLE_FIELDS as $titleField) {
                $fieldNames = $this->fieldArgumentProcessor->excludeField($titleField, $fieldNames);
            }

            foreach ($fieldNames as $fieldName) {
                if (in_array($fieldName, self::PRICE_FIELDS)) {
                    $this->addPriceFieldsToResult($collection, $storeId);
                } elseif ($fieldName != self::STORE_ID_FIELD) {
                    if (is_array($field[0])) {
                        $collection->addFieldToSelect($fieldName);
                    } else {
                        $collection->addFieldToSelect(...$field);
                    }
                }
            }
        }
    }

    /**
     * Add title fields to result
     *
     * @param Collection $collection
     * @param int $storeId
     * @return void
     */
    private function addTitleFieldsToResult(Collection $collection, $storeId)
    {
        if (!$this->isTitleFieldsAddedToResult) {
            $collection->addTitleToResult($storeId);
            $this->isTitleFieldsAddedToResult = true;
        }
    }

    /**
     * Add price fields to result
     *
     * @param Collection $collection
     * @param int $storeId
     * return void
     */
    private function addPriceFieldsToResult(Collection $collection, $storeId)
    {
        if (!$this->isPriceFieldsAddedToResult) {
            $collection->addPriceToResult($storeId);
            $this->isPriceFieldsAddedToResult = true;
        }
    }

    protected function resetState()
    {
        parent::resetState();

        $this->isTitleFieldsAddedToResult = false;
        $this->isPriceFieldsAddedToResult = false;
    }
}
