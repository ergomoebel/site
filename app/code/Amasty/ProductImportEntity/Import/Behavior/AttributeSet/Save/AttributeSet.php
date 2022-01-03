<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\AttributeSet\Save;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ImportCore\Api\BehaviorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class AttributeSet implements BehaviorInterface
{
    const TABLE_NAME = 'eav_attribute_set';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var BehaviorResultInterfaceFactory
     */
    private $behaviorResultFactory;

    public function __construct(
        ResourceConnection $resourceConnection,
        BehaviorResultInterfaceFactory $behaviorResultFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->behaviorResultFactory = $behaviorResultFactory;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->behaviorResultFactory->create();

        $mainTable = $this->getTableName(self::TABLE_NAME);

        $maxId = $this->getMaxId($mainTable);
        $preparedData = $this->prepareDataForTable($data, $mainTable);
        if (!empty($preparedData)) {
            $this->getConnection()->insertOnDuplicate($mainTable, $preparedData);
        }

        $newIds = $this->getNewIds($maxId, $mainTable);
        $uniqueIds = $this->getUniqueIds($preparedData, $mainTable);

        $result->setUpdatedIds(array_diff($uniqueIds, $newIds));
        $result->setNewIds($newIds);

        return $result;
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

    protected function unsetColumns(array $data, array $columns): array
    {
        foreach ($data as &$row) {
            foreach ($columns as $column) {
                unset($row[$column]);
            }
        }

        return $data;
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

    protected function getMaxId($tableName): int
    {
        $select = $this->getConnection()->select()
            ->from($tableName, 'MAX(' . $this->getIdFieldName($tableName) . ')')
            ->limit(1);
        return (int)$this->getConnection()->fetchOne($select);
    }
}
