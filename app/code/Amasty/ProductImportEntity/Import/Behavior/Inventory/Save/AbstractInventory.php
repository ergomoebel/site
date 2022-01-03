<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Inventory\Save;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractDirectBehavior;

abstract class AbstractInventory extends AbstractDirectBehavior
{
    /**
     * @var string
     */
    protected $identityKey;

    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();
        if (empty($data)) {
            return $result;
        }

        list($toInsert, $toUpdate) = $this->prepareDataForSave($data);
        $connection = $this->getConnection();
        $mainTable = $this->getTableName($this->getMainTable());
        $newIds = [];

        foreach ($toInsert as $row) {
            $connection->insertOnDuplicate(
                $mainTable,
                $this->prepareDataForTable([$row], $mainTable)
            );
            $newIds[] = $connection->lastInsertId($mainTable);
        }

        $result->setNewIds($newIds);

        if ($toUpdate) {
            $updatedIds = array_column($toUpdate, $this->getIdentityKey());
            $connection->insertOnDuplicate(
                $mainTable,
                $this->prepareDataForTable($toUpdate, $mainTable)
            );
            $result->setUpdatedIds($updatedIds);
        }

        return $result;
    }

    protected function getIdentityKey(): string
    {
        return (string)$this->identityKey;
    }

    private function prepareDataForSave(array $data): array
    {
        $toInsert = [];
        $toUpdate = [];

        foreach ($data as $row) {
            if (!isset($row[$this->getIdentityKey()])) {
                $toInsert[] = $row;
            } else {
                $toUpdate[] = $row;
            }
        }

        return [$toInsert, $toUpdate];
    }

    /**
     * Get main table name
     *
     * @return string
     */
    abstract protected function getMainTable();
}
