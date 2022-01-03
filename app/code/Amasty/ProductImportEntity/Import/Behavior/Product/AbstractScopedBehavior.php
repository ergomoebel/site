<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;

abstract class AbstractScopedBehavior extends AbstractDirectBehavior
{
    /**
     * @inheritDoc
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();
        $tableRows = $this->prepareTableRows($data);

        $mainTable = $this->getTableName($this->getMainTable());

        $maxId = $this->getMaxId($mainTable);
        $preparedData = $this->prepareDataForTable($tableRows, $mainTable);
        $this->insertData($tableRows, $mainTable);

        $newIds = $this->getNewIds($maxId, $mainTable);
        $uniqueIds = $this->getUniqueIds($preparedData, $mainTable);

        $result->setUpdatedIds(array_diff($uniqueIds, $newIds));
        $result->setNewIds($newIds);

        return $result;
    }

    /**
     * Prepare table rows
     *
     * @param array $data
     * @return array
     */
    private function prepareTableRows(array $data): array
    {
        $scopedKeys = array_flip($this->getScopedKeys());

        /**
         * @param array $row
         * @return bool
         */
        $filterCallback = function (array $row) use ($scopedKeys) {
            $scopedData = array_intersect_key($row, $scopedKeys);
            if ($this->isRowEmpty($scopedData)) {
                return false;
            }

            if (isset($row['store_id']) && !$this->isStoreValid($row['store_id'])) {
                return false;
            }

            return true;
        };

        return array_filter($data, $filterCallback);
    }

    protected function insertData(array $data, string $tableName)
    {
        $this->getConnection()->insertOnDuplicate($tableName, $data);
    }

    /**
     * Get main table name
     *
     * @return string
     */
    abstract protected function getMainTable();

    /**
     * Get scoped field keys
     *
     * @return array
     */
    abstract protected function getScopedKeys();
}
