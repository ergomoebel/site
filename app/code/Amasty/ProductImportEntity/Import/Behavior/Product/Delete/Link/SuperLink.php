<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Delete\Link;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractDirectBehavior;
use Magento\Catalog\Model\ResourceModel\Product\Relation;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class SuperLink extends AbstractDirectBehavior
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

        $linkIds = array_column($data, 'link_id');
        if (empty($linkIds)) {
            return $result;
        }

        $links = $this->getLinks($linkIds);

        $connection = $this->getConnection();
        $connection->delete(
            $this->getTableName('catalog_product_link'),
            $connection->quoteInto('link_id IN (?)', $linkIds)
        );

        $this->deleteRelations($links);

        $result->setDeletedIds($linkIds);

        return $result;
    }

    /**
     * Get stored links
     *
     * @param array $linksIds
     * @return array
     */
    private function getLinks(array $linksIds): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTableName('catalog_product_link'),
            [
                'product_id',
                'linked_product_id'
            ]
        )->where(
            'link_id IN (?)',
            $linksIds
        );

        return $connection->fetchAll($select);
    }

    /**
     * Group links by product Id
     *
     * @param array $links
     * @return array
     */
    private function groupLinksByProductId(array $links): array
    {
        $result = [];
        foreach ($links as $link) {
            $result[$link['product_id']][] = $link['linked_product_id'];
        }

        return $result;
    }

    /**
     * Delete product relations
     *
     * @param array $links
     * @return void
     */
    private function deleteRelations(array $links)
    {
        foreach ($this->groupLinksByProductId($links) as $productId => $linkedProductIds) {
            $this->relationProcessor->removeRelations($productId, $linkedProductIds);
        }
    }
}
