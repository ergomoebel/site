<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\CustomOption;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractDirectBehavior;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractCustomOption extends AbstractDirectBehavior
{
    /**
     * @var IdentityRegistry
     */
    protected $identityRegistry;

    /**
     * @var array
     */
    protected $linkedTableToFieldsMap = [];

    /**
     * @var array
     */
    protected $identityKeys = [];

    /**
     * @var array
     */
    private $identityKeyParts;

    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        BehaviorResultInterfaceFactory $resultFactory,
        IdentityRegistry $identityRegistry
    ) {
        parent::__construct(
            $resourceConnection,
            $storeManager,
            $resultFactory
        );
        $this->identityRegistry = $identityRegistry;
    }

    /**
     * @inheritDoc
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $identityToImportMap = [];
        $nestedData = $this->collectNestedData($data, $identityToImportMap);

        $result = $this->saveMainRows($nestedData, $identityToImportMap);
        $this->saveLinkedRows($nestedData);

        return $result;
    }

    /**
     * Save main rows
     *
     * @param array $nestedData
     * @param array $identityToImportDataMap
     * @return BehaviorResultInterface
     * @throws \Exception
     */
    private function saveMainRows(array &$nestedData, array &$identityToImportDataMap): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();
        if (empty($nestedData)) {
            return $result;
        }

        $mainTable = $this->getTableName($this->getMainTable());

        $maxId = $this->getMaxId($mainTable);
        $preparedData = $this->prepareDataForTable($nestedData, $mainTable);
        $this->getConnection()->insertOnDuplicate($mainTable, $preparedData);

        $newIds = $this->getNewIds($maxId, $mainTable);
        $uniqueIds = $this->getUniqueIds($preparedData, $mainTable);

        $idFieldName = $this->getIdFieldName($mainTable);
        foreach ($uniqueIds as $index => $id) {
            if (!isset($nestedData[$index])) {
                continue;
            }

            if (isset($nestedData[$index]['__linked_tables_rows'])) {
                foreach ($nestedData[$index]['__linked_tables_rows'] as &$tableRows) {
                    $this->setColumnValue($tableRows, $idFieldName, $id);
                }
            }

            $identityKey = $this->getIdentityKey($nestedData[$index]);
            if ($identityKey === null || !isset($identityToImportDataMap[$identityKey])) {
                continue;
            }

            $this->setColumnValue($identityToImportDataMap[$identityKey], $idFieldName, $id);

            $this->registerSubEntities($identityToImportDataMap[$identityKey]);
            $this->markRowsPersisted($identityToImportDataMap[$identityKey]);
        }

        $result->setUpdatedIds(array_diff($uniqueIds, $newIds));
        $result->setNewIds($newIds);

        return $result;
    }

    /**
     * Save rows of linked tables
     *
     * @param array $nestedData
     * @return void
     * @throws \Exception
     */
    private function saveLinkedRows(array $nestedData)
    {
        if (empty($nestedData)) {
            return;
        }

        foreach ($this->linkedTableToFieldsMap as $tableName => $fields) {
            $rows = $this->collectLinkedTableData($nestedData, $tableName);
            if (empty($rows)) {
                continue;
            }

            $this->getConnection()->insertOnDuplicate(
                $this->getTableName($tableName),
                $rows,
                $fields
            );
        }
    }

    /**
     * Get main table name
     *
     * @return string
     */
    abstract protected function getMainTable();

    /**
     * Collects nested data
     *
     * @param array $data
     * @param array $identityToImportDataMap
     * @return array
     */
    private function collectNestedData(array $data, array &$identityToImportDataMap): array
    {
        $result = [];

        $linkedTableKeys = array_map('array_flip', $this->linkedTableToFieldsMap);
        $this->splitByIdentity($data, $identityToImportDataMap);
        foreach ($identityToImportDataMap as $optionRows) {
            $nestedItem = $this->collectNestedDataItem($optionRows, $linkedTableKeys);
            if (!empty($nestedItem)) {
                $result[] = $nestedItem;
            }
        }

        return $result;
    }

    /**
     * Collects nested data item
     *
     * @param array $rows
     * @param array $linkedTableKeys
     * @return array
     */
    private function collectNestedDataItem(array $rows, array $linkedTableKeys)
    {
        $dataItem = [];

        foreach ($rows as $row) {
            if ($this->isNestedDataRowPersisted($row)) {
                continue;
            }

            $storeId = $row['store_id'] ?? 0;
            if ($storeId == 0) {
                $dataItem = $row;
            }

            if ($this->isStoreValid($storeId)) {
                foreach ($linkedTableKeys as $tableName => $keys) {
                    $linkedTableRow = array_intersect_key($row, $keys);
                    if (!$this->isRowEmpty($linkedTableRow)) {
                        $linkedTableRow['store_id'] = $storeId;
                        $dataItem['__linked_tables_rows'][$tableName][] = $linkedTableRow;
                    }
                }
            }
        }

        return $dataItem;
    }

    /**
     * Checks if nested data row is already persisted
     *
     * @param array $row
     * @return bool
     */
    protected function isNestedDataRowPersisted(array $row)
    {
        return false;
    }

    /**
     * Mark rows as persisted
     *
     * @param array $data
     * @return void
     */
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    protected function markRowsPersisted(array $data)
    {
    }

    /**
     * Split import data by identity
     *
     * @param array $data
     * @param array $identityToImportDataMap
     * @return void
     */
    private function splitByIdentity(array $data, array &$identityToImportDataMap): void
    {
        foreach ($data as $row) {
            $key = $this->getIdentityKey($row);
            if ($key === null) {
                continue;
            }
            $identityToImportDataMap[$key][] = $row;
        }
    }

    /**
     * Gets identity key for import data row
     *
     * @param array $row
     * @param array|null $keys
     * @return string|null
     */
    protected function getIdentityKey(array $row, array $keys = null)
    {
        if ($keys !== null) {
            $keyParts = array_flip($keys);
        } else {
            if (!$this->identityKeyParts) {
                $this->identityKeyParts = array_flip($this->identityKeys);
            }

            $keyParts = $this->identityKeyParts;
        }

        $keyParts = array_intersect_key($row, $keyParts);
        if (empty($keyParts)) {
            return null;
        }

        return implode('-', $keyParts);
    }

    /**
     * Register sub entity identity keys.
     * Used for avoiding sub entity data double save
     *
     * @param array $data
     * @return void
     */
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    protected function registerSubEntities(array $data)
    {
    }

    /**
     * Collect data for linked table
     *
     * @param array $nestedData
     * @param string $tableName
     * @return array
     */
    private function collectLinkedTableData(array $nestedData, string $tableName): array
    {
        $result = [];
        foreach ($nestedData as $nestedDataItem) {
            if (!isset($nestedDataItem['__linked_tables_rows'][$tableName])) {
                continue;
            }

            foreach ($nestedDataItem['__linked_tables_rows'][$tableName] as $row) {
                $result[] = $row;
            }
        }

        return $result;
    }
}
