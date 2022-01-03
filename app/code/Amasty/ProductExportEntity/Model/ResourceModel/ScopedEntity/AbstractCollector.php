<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity;

use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\Collection\Field\ArgumentProcessor;
use Magento\Framework\Data\Collection\AbstractDb;

abstract class AbstractCollector implements ItemCollectorInterface
{
    /**
     * @var ArgumentProcessor
     */
    protected $fieldArgumentProcessor;

    /**
     * @var array
     */
    protected $fieldsToSelect = [];

    /**
     * @var array
     */
    protected $fieldsToFilter = [];

    public function __construct(ArgumentProcessor $fieldArgumentProcessor)
    {
        $this->fieldArgumentProcessor = $fieldArgumentProcessor;
    }

    public function setFieldsToSelect(array $fieldsToSelect)
    {
        $this->fieldsToSelect = $fieldsToSelect;
        return $this;
    }

    public function setFieldsToFilter(array $fieldsToFilter)
    {
        $this->fieldsToFilter = $fieldsToFilter;
        return $this;
    }

    public function collect(AbstractDb $collection, array $defaultItems, $scope)
    {
        // This abstract implementation currently doesn't support collect entities for several scopes at the same time.
        // Assumed that this will be reimplemented
        // after \Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntityCollection::loadData() refactoring
        // to implement entities data collecting for all of required scopes by a single collect() method call
        $scopeValue = is_array($scope)
            ? current($scope)
            : $scope;
        $this->prepareCollection($collection, $scopeValue);
        $items = $this->performCollect(
            $collection,
            $defaultItems,
            $scopeValue
        );

        $this->resetState();

        return $items;
    }

    /**
     * Perform scoped entity items collect
     *
     * @param AbstractDb $collection
     * @param array $defaultItems
     * @param int $scopeValue
     * @return array
     */
    abstract protected function performCollect(
        AbstractDb $collection,
        array $defaultItems,
        $scopeValue
    );

    /**
     * Prepare entity collection
     *
     * @param AbstractDb $collection
     * @param int $scopeValue
     * @return AbstractDb
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareCollection(AbstractDb $collection, $scopeValue)
    {
        foreach ($this->fieldsToSelect as $field) {
            $collection->addFieldToSelect(...$field);
        }
        foreach ($this->fieldsToFilter as $field) {
            $collection->addFieldToFilter(...$field);
        }

        return $collection;
    }

    /**
     * Prepare entity data
     *
     * @param array $entityData
     * @param array $defaultItems
     * @param string $idFieldName
     * @param string $scopeField
     * @param int $scopeValue
     * @return array
     */
    protected function prepareEntityData(
        array $entityData,
        array $defaultItems,
        $idFieldName,
        $scopeField,
        $scopeValue
    ) {
        $result = [];
        foreach ($this->getFieldsToSelectAsArray() as $fieldName) {
            if (isset($entityData[$fieldName])) {
                if ($fieldName == $scopeField) {
                    $result[$fieldName] = $scopeValue;
                } else {
                    $result[$fieldName] = $this->getFieldValue(
                        $fieldName,
                        $defaultItems,
                        $entityData,
                        $idFieldName
                    );
                }
            }
        }
        return $result;
    }

    /**
     * Get field value according to store or default value
     *
     * @param string $fieldName
     * @param array $defaultData
     * @param array $data
     * @param string $idFieldName
     * @return mixed
     */
    protected function getFieldValue($fieldName, array $defaultData, array $data, $idFieldName)
    {
        $itemId = $data[$idFieldName];

        if (array_key_exists($fieldName, $data) && $data[$fieldName] !== null) {
            return $data[$fieldName];
        }

        if (array_key_exists($itemId, $defaultData)
            && array_key_exists($fieldName, $defaultData[$itemId])
        ) {
            return $defaultData[$itemId][$fieldName];
        }

        return null;
    }

    /**
     * Get fields to select as array
     *
     * @return array
     */
    protected function getFieldsToSelectAsArray()
    {
        $result = [];
        foreach ($this->fieldsToSelect as $field) {
            $fields = $this->fieldArgumentProcessor->getFieldNames($field);
            foreach ($fields as $fieldToAdd) {
                if (!in_array($fieldToAdd, $result)) {
                    $result[] = $fieldToAdd;
                }
            }
        }
        return $result;
    }

    /**
     * Get fields to filter as array
     *
     * @return array
     */
    protected function getFieldsToFilterAsArray()
    {
        $result = [];
        foreach ($this->fieldsToFilter as $field) {
            $fields = $this->fieldArgumentProcessor->getFieldNames($field);
            foreach ($fields as $fieldToAdd) {
                if (!in_array($fieldToAdd, $result)) {
                    $result[] = $fieldToAdd;
                }
            }
        }
        return $result;
    }

    /**
     * Reset collector's state
     *
     * @return void
     */
    protected function resetState()
    {
        $this->fieldsToSelect = [];
        $this->fieldsToFilter = [];
    }
}
