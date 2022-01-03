<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ImportCore\Api\BehaviorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractDirectBehavior implements BehaviorInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BehaviorResultInterfaceFactory
     */
    protected $resultFactory;

    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        BehaviorResultInterfaceFactory $resultFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Get resource connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    protected function getConnection()
    {
        return $this->resourceConnection->getConnection();
    }

    /**
     * Get table name
     *
     * @param string $table
     * @return string
     * @throws \Exception
     */
    protected function getTableName($table)
    {
        return $this->resourceConnection->getTableName($table);
    }

    /**
     * Get Id field name of specified table
     *
     * @param string $tableName
     * @return string
     * @throws \Exception
     */
    protected function getIdFieldName($tableName)
    {
        // todo: store per table
        return $this->getConnection()->getAutoIncrementField($tableName);
    }

    protected function getUniqueIds(array &$data, $tableName)
    {
        return array_filter(array_unique(array_column($data, $this->getIdFieldName($tableName))));
    }

    protected function getNewIds(int $minId, $tableName): array
    {
        $select = $this->getConnection()->select()
            ->from($tableName, $this->getIdFieldName($tableName))
            ->where($this->getIdFieldName($tableName) . ' > ' . $minId);

        return $this->getConnection()->fetchCol($select);
    }

    protected function getMaxId($tableName): int
    {
        $select = $this->getConnection()->select()
            ->from($tableName, 'MAX(' . $this->getIdFieldName($tableName) . ')')
            ->limit(1);

        return (int)$this->getConnection()->fetchOne($select);
    }

    /**
     * Retrieves existing ids
     *
     * @param array $ids
     * @param string $tableName
     * @param string|null $idFieldName
     * @return array
     * @throws \Exception
     */
    protected function getExistingIds(array $ids, $tableName, $idFieldName = null): array
    {
        if (!$idFieldName) {
            $idFieldName = $this->getIdFieldName($tableName);
        }

        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($tableName, [$idFieldName])
            ->where($idFieldName . ' IN (?)', $ids);

        return $connection->fetchCol($select);
    }

    protected function prepareDataForTable(array $data, string $tableName)
    {
        if (empty($data)) {
            return $data;
        }

        $columns = $this->getConnection()->describeTable($tableName);
        $columnsToUnset = current($data) ? array_keys(current($data)) : [];
        foreach ($columns as $column => $value) {
            if (false !== $key = array_search($column, $columnsToUnset)) {
                unset($columnsToUnset[$key]);
            }
        }

        if (!empty($columnsToUnset)) {
            $data = $this->unsetColumns($data, $columnsToUnset);
        }

        return $data;
    }

    protected function unsetColumns(array $data, array $columns): array
    {
        foreach ($data as &$row) {
            foreach ($columns as $column) {
                unset($row[$column]);
            }
        }

        return $data;
    }

    protected function setColumnValue(array &$data, string $columnName, $value): array
    {
        foreach ($data as &$row) {
            $row[$columnName] = $value;
        }

        return $data;
    }

    protected function isStoreValid($storeId): bool
    {
        if ($storeId == 0) {
            return true;
        }

        // todo: revise, store check results
        try {
            $this->storeManager->getStore($storeId);

            return true;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * Checks if data row is empty
     *
     * @param array $row
     * @return bool
     */
    protected function isRowEmpty(array $row): bool
    {
        foreach ($row as $item) {
            if (!empty($item)) {
                return false;
            }
        }

        return true;
    }
}
