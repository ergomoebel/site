<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CmsImportEntity
 */


declare(strict_types=1);

namespace Amasty\CmsImportEntity\Model\ResourceModel\Store;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;

class RelationPersistence
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Save default relations for entities
     *
     * @param array $entitiesData
     * @param string $entityType
     * @throws \Exception
     */
    public function saveDefault(array $entitiesData, string $entityType): void
    {
        $metadata = $this->metadataPool->getMetadata($entityType);
        $linkField = $metadata->getLinkField();

        $linkValues = array_column($entitiesData, $linkField);
        if (empty($linkValues)) {
            return;
        }

        $existingLinkValues = $this->getExistingLinkValues($linkValues, $metadata);
        $toInsert = array_diff($linkValues, $existingLinkValues);
        if ($toInsert) {
            $getDefaultRelationsCallback = function ($linkValue) use ($linkField) {
                return [
                    $linkField => $linkValue,
                    'store_id' => 0
                ];
            };
            $relations = array_map($getDefaultRelationsCallback, $toInsert);

            $this->performInsert($relations, $metadata);
        }
    }

    /**
     * Retrieves existing link values from relation table
     *
     * @param array $linkValues
     * @param EntityMetadataInterface $metadata
     * @return array
     */
    private function getExistingLinkValues(array $linkValues, EntityMetadataInterface $metadata)
    {
        $connectionName = $metadata->getEntityConnectionName();
        $connection = $this->resourceConnection->getConnectionByName($connectionName);

        $entityTable = $this->resourceConnection->getTableName(
            $metadata->getEntityTable(),
            $connectionName
        );
        $select = $connection->select()
            ->from($this->getRelationTableName($entityTable), [$metadata->getLinkField()])
            ->where($metadata->getLinkField() . ' IN (?)', $linkValues);

        return $connection->fetchCol($select);
    }

    /**
     * Get relation table name
     *
     * @param string $entityTableName
     * @return string
     */
    private function getRelationTableName(string $entityTableName): string
    {
        return $entityTableName . '_store';
    }

    /**
     * Save relations
     *
     * @param array $relations
     * @param string $entityType
     * @throws \Exception
     */
    public function save(array $relations, string $entityType)
    {
        $metadata = $this->metadataPool->getMetadata($entityType);
        $linkField = $metadata->getLinkField();

        $linkValues = array_column($relations, $linkField);
        if (empty($linkValues)) {
            return;
        }

        $this->deleteExistingRelations($linkValues, $metadata);
        $this->performInsert($relations, $metadata);
    }

    /**
     * Performs insertion relations data into relation table
     *
     * @param array $relations
     * @param EntityMetadataInterface $metadata
     */
    private function performInsert(array $relations, EntityMetadataInterface $metadata)
    {
        $connectionName = $metadata->getEntityConnectionName();
        $connection = $this->resourceConnection->getConnectionByName($connectionName);

        $entityTable = $this->resourceConnection->getTableName(
            $metadata->getEntityTable(),
            $connectionName
        );
        $connection->insertMultiple($this->getRelationTableName($entityTable), $relations);
    }

    /**
     * Delete existing relation rows
     *
     * @param array $linkValues
     * @param EntityMetadataInterface $metadata
     */
    private function deleteExistingRelations(array $linkValues, EntityMetadataInterface $metadata)
    {
        $connectionName = $metadata->getEntityConnectionName();
        $connection = $this->resourceConnection->getConnectionByName($connectionName);

        $entityTable = $this->resourceConnection->getTableName(
            $metadata->getEntityTable(),
            $connectionName
        );

        $connection->delete(
            $this->getRelationTableName($entityTable),
            [$metadata->getLinkField() . ' IN (?)' => $linkValues]
        );
    }
}
