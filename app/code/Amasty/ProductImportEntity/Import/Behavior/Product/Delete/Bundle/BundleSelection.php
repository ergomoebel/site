<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Delete\Bundle;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractDirectBehavior;
use Magento\Catalog\Model\ResourceModel\Product\Relation;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class BundleSelection extends AbstractDirectBehavior
{
    /**
     * @var Relation
     */
    private $relationProcessor;

    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        BehaviorResultInterfaceFactory $resultFactory,
        Relation $relationProcessor
    ) {
        parent::__construct(
            $resourceConnection,
            $storeManager,
            $resultFactory
        );
        $this->relationProcessor = $relationProcessor;
    }

    /**
     * @inheritDoc
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();

        $selectionIds = array_column($data, 'selection_id');
        if (empty($selectionIds)) {
            return $result;
        }

        $selections = $this->getSelections($selectionIds);

        $connection = $this->getConnection();
        $connection->delete(
            $this->getTableName('catalog_product_bundle_selection'),
            $connection->quoteInto('selection_id IN (?)', $selectionIds)
        );

        $this->deleteRelations($selections);

        $result->setDeletedIds($selectionIds);

        return $result;
    }

    /**
     * Get stored selections
     *
     * @param array $selectionIds
     * @return array
     */
    private function getSelections(array $selectionIds): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTableName('catalog_product_bundle_selection'),
            [
                'parent_product_id',
                'product_id'
            ]
        )->where(
            'selection_id IN (?)',
            $selectionIds
        );

        return $connection->fetchAll($select);
    }

    /**
     * Group selections by parent product Id
     *
     * @param array $selections
     * @return array
     */
    private function groupSelectionsByParentProductId(array $selections): array
    {
        $result = [];
        foreach ($selections as $selection) {
            $result[$selection['parent_product_id']][] = $selection['product_id'];
        }

        return $result;
    }

    /**
     * Delete product relations
     *
     * @param array $selections
     * @return void
     */
    private function deleteRelations(array $selections)
    {
        foreach ($this->groupSelectionsByParentProductId($selections) as $parentProductId => $productIds) {
            $this->relationProcessor->removeRelations($parentProductId, $productIds);
        }
    }
}
