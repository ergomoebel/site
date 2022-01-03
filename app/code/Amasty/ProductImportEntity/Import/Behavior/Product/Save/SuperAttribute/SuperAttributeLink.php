<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\SuperAttribute;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractDirectBehavior;
use Magento\Catalog\Model\ResourceModel\Product\Relation;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class SuperAttributeLink extends AbstractDirectBehavior
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
        $result = $this->saveLinks($data);
        $this->addRelations($data);

        return $result;
    }

    /**
     * Save super links rows
     *
     * @param array $data
     * @return BehaviorResultInterface
     * @throws \Exception
     */
    private function saveLinks(array &$data): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();
        if (empty($data)) {
            return $result;
        }

        $links = $this->getLinks($data);

        $newIds = [];
        $updatedIds = [];
        $connection = $this->getConnection();
        foreach ($data as &$item) {
            $identityKey = isset($item['product_id']) && isset($item['parent_id'])
                ? $item['product_id'] . '-' . $item['parent_id']
                : null;

            if ($identityKey && isset($links[$identityKey])) {
                $existentLinkId = $links[$identityKey]['link_id'];

                $updatedIds[] = $existentLinkId;
                $item['link_id'] = $existentLinkId;

                continue;
            }

            if ($identityKey) {
                $tableName = $this->getTableName('pref_catalog_product_super_link');

                $bind = [
                    'product_id' => $item['product_id'],
                    'parent_id' => $item['parent_id'],
                ];
                if (isset($item['link_id'])) {
                    $bind['link_id'] = $item['link_id'];
                }
                $connection->insertOnDuplicate($tableName, [$bind]);
                $newLinkId = $connection->lastInsertId($tableName);

                $newIds[] = $newLinkId;
                $item['link_id'] = $newLinkId;
            }
        }

        $result->setUpdatedIds($updatedIds);
        $result->setNewIds($newIds);

        return $result;
    }

    /**
     * Get stored super links
     *
     * @param array $data
     * @return array
     */
    private function getLinks(array $data): array
    {
        $connection = $this->getConnection();

        $conditions = [];
        foreach ($data as $item) {
            if (isset($item['product_id']) && isset($item['parent_id'])) {
                $conditions[] = implode(
                    ' AND ',
                    [
                        $connection->quoteInto('product_id = ?', $item['product_id']),
                        $connection->quoteInto('parent_id = ?', $item['parent_id'])
                    ]
                );
            }
        }
        $select = $connection->select()->from(
            $this->getTableName('pref_catalog_product_super_link'),
            [
                'link_id',
                'product_id',
                'parent_id'
            ]
        )->where(
            '(' . implode(' OR ', $conditions) . ')'
        );

        $links = [];
        $linkRows = $connection->fetchAll($select);
        foreach ($linkRows as $linkData) {
            $links[$linkData['product_id'] . '-' . $linkData['parent_id']] = $linkData;
        }

        return $links;
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
            if (isset($row['parent_id']) && isset($row['product_id'])) {
                $this->relationProcessor->addRelation(
                    $row['parent_id'],
                    $row['product_id']
                );
            }
        }
    }
}
