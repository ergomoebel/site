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
use Magento\Bundle\Model\Selection;
use Magento\Catalog\Model\ResourceModel\Product\Relation;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class BundleSelection extends AbstractDirectBehavior
{
    /**
     * @var SequenceHandler
     */
    private $sequenceHandler;

    /**
     * @var Relation
     */
    private $relationProcessor;

    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        BehaviorResultInterfaceFactory $resultFactory,
        SequenceHandler $sequenceHandler,
        Relation $relationProcessor
    ) {
        parent::__construct(
            $resourceConnection,
            $storeManager,
            $resultFactory
        );
        $this->sequenceHandler = $sequenceHandler;
        $this->relationProcessor = $relationProcessor;
    }

    /**
     * @inheritDoc
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->saveSelections($data);
        $this->addRelations($data);

        return $result;
    }

    /**
     * Save bundle selections
     *
     * @param array $data
     * @return BehaviorResultInterface
     */
    private function saveSelections(array &$data): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();
        if (empty($data)) {
            return $result;
        }

        $mainTable = $this->getTableName('catalog_product_bundle_selection');
        $idFieldName = $this->getIdFieldName($mainTable);
        $preparedData = $this->prepareDataForTable($data, $mainTable, $idFieldName);

        $uniqueIds = $this->getUniqueIds($preparedData, $mainTable);
        $existingIds = $this->getExistingIds($uniqueIds, $mainTable);

        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            $this->sequenceHandler->handleUpdate($preparedData, Selection::class);
            $connection->insertOnDuplicate($mainTable, $preparedData);

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        foreach ($uniqueIds as $index => $id) {
            $data[$index][$idFieldName] = $id;
        }

        $result->setUpdatedIds(array_intersect($uniqueIds, $existingIds));
        $result->setNewIds(array_diff($uniqueIds, $existingIds));

        return $result;
    }

    /**
     * Add product relations
     *
     * @param array $data
     * @return void
     */
    private function addRelations(array $data)
    {
        foreach ($data as $row) {
            if (isset($row['parent_product_id']) && isset($row['product_id'])) {
                $this->relationProcessor->addRelation(
                    $row['parent_product_id'],
                    $row['product_id']
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function getIdFieldName($tableName)
    {
        return 'selection_id';
    }
}
