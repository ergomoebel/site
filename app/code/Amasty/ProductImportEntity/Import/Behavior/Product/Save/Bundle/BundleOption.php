<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\Bundle;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractDirectBehavior;
use Amasty\ProductImportEntity\Model\EntityManager\SequenceHandler;
use Magento\Bundle\Api\Data\OptionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class BundleOption extends AbstractDirectBehavior
{
    /**
     * @var SequenceHandler
     */
    private $sequenceHandler;

    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        BehaviorResultInterfaceFactory $resultFactory,
        SequenceHandler $sequenceHandler
    ) {
        parent::__construct(
            $resourceConnection,
            $storeManager,
            $resultFactory
        );
        $this->sequenceHandler = $sequenceHandler;
    }

    /**
     * @inheritDoc
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();
        if (empty($data)) {
            return $result;
        }

        list($toInsert, $toUpdate) = $this->prepareDataForSave($data);

        $connection = $this->getConnection();
        $mainTable = $this->getTableName('catalog_product_bundle_option');

        if ($toInsert) {
            $newIds = [];

            $withIds = $this->prepareDataForTable(
                $toInsert['__with_id'] ?? [],
                $mainTable
            );
            if (!empty($withIds)) {
                $newIds = array_column($withIds, 'option_id');
                $connection->insertOnDuplicate($mainTable, $withIds, $withIds);
            }

            $withoutIds = $toInsert['__without_id'] ?? [];
            if (!empty($withoutIds)) {
                foreach ($withoutIds as &$row) {
                    $preparedRow = $this->prepareDataForTable([$row], $mainTable);
                    $connection->insertOnDuplicate($mainTable, $preparedRow[0]);
                    if (!isset($row['option_id'])) {
                        $newOptionId = $connection->lastInsertId($mainTable);
                        $row['option_id'] = $newOptionId;
                        $newIds[] = $newOptionId;
                    } else {
                        $newIds[] = $row['option_id'];
                    }
                }
            }

            $result->setNewIds($newIds);
        }

        if ($toUpdate) {
            $updatedIds = $newIds = array_column($toUpdate, 'option_id');
            $connection->insertOnDuplicate(
                $mainTable,
                $this->prepareDataForTable($toUpdate, $mainTable),
                [
                    'required',
                    'position',
                    'type'
                ]
            );

            $result->setUpdatedIds($updatedIds);
        }

        return $result;
    }

    /**
     * Get stored bundle options
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    private function getBundleOptions(array &$data): array
    {
        $connection = $this->getConnection();

        $optionIds = array_column($data, 'option_id');
        if (empty($optionIds)) {
            return [];
        }

        $select = $connection->select()->from(
            $this->getTableName('catalog_product_bundle_option'),
            ['option_id', 'parent_id']
        )->where(
            'option_id IN (?)',
            $optionIds
        );

        return $connection->fetchAll($select);
    }

    /**
     * Groups bundle options using specified data keys
     *
     * @param array $bundleOptions
     * @param array $keys
     * @return array
     */
    private function groupBundleOptions(array $bundleOptions, array $keys): array
    {
        $keysArray = array_flip($keys);

        $groupedOptions = [];
        foreach ($bundleOptions as $bundleOption) {
            $keyParts = array_intersect_key($bundleOption, $keysArray);
            $key = implode('-', $keyParts);
            $groupedOptions[$key] = $bundleOption;
        }

        return $groupedOptions;
    }

    /**
     * Prepares data for saving
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    private function prepareDataForSave(array &$data): array
    {
        $bundleOptions = $this->getBundleOptions($data);
        $bundleOptsById = $this->groupBundleOptions($bundleOptions, ['option_id']);
        $bundleOptsByIdAndParentId = $this->groupBundleOptions(
            $bundleOptions,
            ['option_id', 'parent_id']
        );

        $toInsert = [];
        $toUpdate = [];
        foreach ($data as &$row) {
            if (!isset($row['option_id'])) {
                $toInsert['__without_id'][] = &$row;
            } else {
                $optionId = $row['option_id'];
                if (isset($bundleOptsById[$optionId])) {
                    if (isset($row['parent_id'])
                        && isset($bundleOptsByIdAndParentId[$optionId . '-' . $row['parent_id']])
                    ) {
                        $toUpdate[] = $row;
                    } else {
                        unset($row['option_id']);
                        $toInsert['__without_id'][] = &$row;
                    }
                } else {
                    $toInsert['__with_id'][] = $row;
                }
            }
        }

        if (isset($toInsert['__without_id'])) {
            $this->sequenceHandler->handleNew(
                $toInsert['__without_id'],
                OptionInterface::class
            );
        }
        if (isset($toInsert['__with_id'])) {
            $this->sequenceHandler->handleUpdate(
                $toInsert['__with_id'],
                OptionInterface::class
            );
        }
        $this->sequenceHandler->handleUpdate($toUpdate, OptionInterface::class);

        return [$toInsert, $toUpdate];
    }
}
