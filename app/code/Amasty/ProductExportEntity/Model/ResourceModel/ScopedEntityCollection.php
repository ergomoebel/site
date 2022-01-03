<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel;

use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\Collection\Field\ArgumentProcessor;
use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\ItemCollectorInterface;
use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb as DbCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

abstract class ScopedEntityCollection extends Collection implements SourceProviderInterface
{
    const SCOPE_FILTER_CONDITIONS = ['in', 'nin'];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ItemCollectorInterface
     */
    private $itemCollector;

    /**
     * @var ArgumentProcessor
     */
    private $fieldArgumentProcessor;

    /**
     * @var AbstractDb
     */
    private $resource;

    /**
     * @var array
     */
    private $fieldsToSelect = [];

    /**
     * @var array
     */
    private $fieldsToFilter = [];

    /**
     * @var array|null
     */
    private $scopeValues;

    /**
     * @var bool
     */
    private $isLoadByScopes = false;

    /**
     * @var array|null|int
     */
    private $scopeFilter = null;

    /**
     * @var array
     */
    private $defaultItemsData = [];

    public function __construct(
        EntityFactoryInterface $entityFactory,
        StoreManagerInterface $storeManager,
        ItemCollectorInterface $itemCollector,
        ArgumentProcessor $fieldArgumentProcessor
    ) {
        parent::__construct($entityFactory);
        $this->storeManager = $storeManager;
        $this->itemCollector = $itemCollector;
        $this->fieldArgumentProcessor = $fieldArgumentProcessor;
    }

    public function getMainTable()
    {
        return $this->getResource()->getMainTable();
    }

    public function getIdFieldName()
    {
        return $this->getResource()->getIdFieldName();
    }

    public function getSelect()
    {
        return $this->getResource()->getConnection()
            ->select();
    }

    /**
     * Get scope field name
     *
     * @return string
     */
    abstract protected function getScopeFieldName();

    /**
     * Get scope type
     *
     * @return string
     */
    protected function getScopeType()
    {
        return ScopeInterface::SCOPE_STORE;
    }

    /**
     * Get collection factory
     *
     * @return object
     */
    abstract protected function getCollectionFactory();

    /**
     * Get fields that are allowed being redundant for different scopes
     *
     * @return array
     */
    protected function getRedundantFields()
    {
        return [$this->getIdFieldName(), $this->getScopeFieldName()];
    }

    public function addFieldToSelect($fieldName, $alias = null)
    {
        $scopeFieldName = $this->getScopeFieldName();
        if ($this->fieldArgumentProcessor->hasField($scopeFieldName, $fieldName)) {
            $this->isLoadByScopes = true;

            $fieldName = $this->fieldArgumentProcessor->excludeField(
                $scopeFieldName,
                $fieldName
            );
        }

        if ($fieldName !== null) {
            $this->fieldsToSelect[] = [$fieldName, $alias];
        }

        return $this;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        $scopeFieldName = $this->getScopeFieldName();
        if ($this->fieldArgumentProcessor->hasField($scopeFieldName, $field)) {
            $this->isLoadByScopes = true;
            $this->scopeFilter = $condition;

            $field = $this->fieldArgumentProcessor->excludeField(
                $scopeFieldName,
                $field
            );
        }

        if ($field !== null) {
            $this->fieldsToFilter[] = [$field, $condition];
        }

        return $this;
    }

    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_items = [];

        $items = [];
        if ($this->isLoadByScopes) {
            $this->defaultItemsData = [];
            foreach ($this->getScopeValues() as $scopeId) {
                $this->collectItems($scopeId, $items);
            }
        } else {
            $this->collectItems(0, $items);
        }

        if ($this->isLoadByScopes) {
            $items = $this->removeRedundantValues($items);
        }

        foreach ($items as $itemData) {
            $item = $this->getNewEmptyItem()
                ->setData($itemData);
            $this->_addItem($item);
        }

        $this->fieldsToSelect = [];
        $this->fieldsToFilter = [];
        $this->scopeValues = null;
        $this->isLoadByScopes = false;
        $this->scopeFilter = null;

        $this->_setIsLoaded();

        return $this;
    }

    /**
     * Get collection data
     *
     * @return array
     */
    public function getData()
    {
        $this->load();

        return $this->toArray()['items'];
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        $this->itemCollector->setFieldsToSelect($this->fieldsToSelect)
            ->setFieldsToFilter($this->fieldsToFilter);

        /** @var DbCollection $collection */
        $collection = $this->getCollectionFactory()->create();
        $scopeCount = $this->isLoadByScopes ? count($this->getScopeValues()) : 1;
        $items = $this->itemCollector->collect($collection, [], 0);

        return count($items) * $scopeCount;
    }

    /**
     * Reset loaded for collection data array
     *
     * @return ScopedEntityCollection
     */
    public function resetData()
    {
        $this->clear();

        return $this;
    }

    /**
     * Get resource model
     *
     * @return AbstractDb
     */
    public function getResource()
    {
        if (!$this->resource) {
            /** @var DbCollection $collection */
            $collection = $this->getCollectionFactory()->create();
            $this->resource = $collection->getResource();
        }

        return $this->resource;
    }

    /**
     * Get scope values
     *
     * @return array
     */
    private function getScopeValues()
    {
        if (!$this->scopeValues) {
            $scopeValues = [];

            $scopeType = $this->getScopeType();
            if ($scopeType == ScopeInterface::SCOPE_STORE) {
                foreach ($this->storeManager->getStores(true) as $store) {
                    $scopeValues[] = $store->getId();
                }
            } elseif ($scopeType == ScopeInterface::SCOPE_WEBSITE) {
                foreach ($this->storeManager->getWebsites(true) as $website) {
                    $scopeValues[] = $website->getId();
                }
            }

            if ($this->scopeFilter !== null) {
                $scopeValues = $this->filterScopes($scopeValues, $this->scopeFilter);
            }

            sort($scopeValues);

            $this->scopeValues = $scopeValues;
        }

        return $this->scopeValues;
    }

    /**
     * Check if collection has default scope
     *
     * @return bool
     */
    private function hasDefaultScope()
    {
        foreach ($this->getScopeValues() as $scope) {
            if ($scope == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter scope values
     *
     * @param array $scopes
     * @param array|int $filter
     * @return array
     */
    private function filterScopes(array $scopes, $filter)
    {
        $condition = 'eq';
        if (is_array($filter)) {
            $keys = array_keys($filter);
            if (array_intersect($keys, self::SCOPE_FILTER_CONDITIONS)) {
                $condition = $keys[0];
                $value = $filter[$condition];
            } else {
                $condition = 'in';
                $value = $filter;
            }
        } else {
            $value = $filter;
        }

        if ($value === null) {
            return $scopes;
        }

        /**
         * Filter scopes callback
         *
         * @param int $scope
         * @return bool
         */
        $filterCallback = function ($scope) use ($value, $condition) {
            switch ($condition) {
                case 'eq':
                    return $scope == $value || $value == 0;
                case 'in':
                    return in_array($scope, $value) || in_array(0, $value);
                case 'nin':
                    return !in_array($scope, $value) && !in_array(0, $value);
                default:
                    return true;
            }
        };

        return array_filter($scopes, $filterCallback);
    }

    /**
     * Collect items for scope value
     *
     * @param int $scopeValue
     * @param array $itemsData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function collectItems($scopeValue, array &$itemsData)
    {
        $this->itemCollector->setFieldsToSelect($this->fieldsToSelect)
            ->setFieldsToFilter($this->fieldsToFilter);

        /** @var DbCollection $collection */
        $collection = $this->getCollectionFactory()->create();
        $items = $this->itemCollector->collect(
            $collection,
            $this->defaultItemsData,
            $scopeValue
        );

        foreach ($items as $itemData) {
            if ($this->isLoadByScopes) {
                $itemData[$this->getScopeFieldName()] = $scopeValue;

                if ($scopeValue == 0) {
                    $this->defaultItemsData[$this->getIdFieldName()] = $itemData;
                }
            }

            $itemsData[] = $itemData;
        }

        $collection = null;
    }

    /**
     * Remove redundant values from items of non-default scopes
     *
     * | scope   | column A        | column B        |
     * |---------------------------------------------|
     * | default | default_value_a | default_value_b |
     * |---------------------------------------------|
     * | store 1 | store1_value_a  | default_value_b <- To remove!
     * |----------------------------------------------
     * ...
     *
     * @param array $items
     * @return array
     */
    private function removeRedundantValues(array $items)
    {
        $processedItems = [];

        $hasDefaultScope = $this->hasDefaultScope();

        $groupedItems = $this->reGroupByItemId($items);
        $redundantFields = $this->getRedundantFields();
        foreach ($groupedItems as $group) {
            $groupItemsCount = count($group);
            if ($groupItemsCount > 1 && $hasDefaultScope) {
                $defaultItem = $group[0];

                $columns = array_keys($defaultItem);
                $columns = array_diff($columns, $redundantFields);

                $processedItems[] = $defaultItem;

                $index = 1;
                while ($index < $groupItemsCount) {
                    $item = $group[$index];
                    foreach ($columns as $column) {
                        if (isset($item[$column])
                            && $item[$column] == $defaultItem[$column]
                        ) {
                            unset($item[$column]);
                        }
                    }

                    $processedItems[] = $item;
                    $index++;
                }
            } else {
                foreach ($group as $item) {
                    $processedItems[] = $item;
                }
            }
        }

        return $processedItems;
    }

    /**
     * Re-group items by Ids
     *
     * @param array $items
     * @return array
     */
    private function reGroupByItemId(array $items)
    {
        $groupedByIds = [];

        $itemIds = array_column($items, $this->getIdFieldName());
        $itemIds = array_unique($itemIds);

        $idFieldName = $this->getIdFieldName();
        foreach ($itemIds as $itemId) {

            /**
             * Filter by item Id callback
             *
             * @param array $item
             * @return bool
             */
            $filterByItemIdCallback = function (array $item) use ($idFieldName, $itemId) {
                return isset($item[$idFieldName])
                    && $item[$idFieldName] == $itemId;
            };
            $itemsOfItemId = array_filter($items, $filterByItemIdCallback);

            $groupedByIds[] = array_values($itemsOfItemId);
        }

        return $groupedByIds;
    }
}
