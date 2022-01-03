<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CmsImportEntity
 */

declare(strict_types=1);

namespace Amasty\CmsImportEntity\Import\Validation\RelationValidator;

use Amasty\ImportCore\Api\Validation\RelationValidatorInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

class BlockStoreValidator implements RelationValidatorInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var string|null
     */
    private $message;

    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $entityRow, array $subEntityRows): bool
    {
        if (isset($entityRow['identifier'])) {
            $storeIds = array_column($subEntityRows, 'store_id');
            if (!$this->isIdentifierUnique($entityRow['identifier'], $storeIds)) {
                $this->message = (string)__(
                    'A block identifier %1 already exists in the specified stores: %2.',
                    $entityRow['identifier'],
                    implode(',', $storeIds)
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Checks if identifier is unique in given scopes
     *
     * @param string $identifier
     * @param array $storeIds
     * @return bool
     * @throws \Exception
     */
    private function isIdentifierUnique(string $identifier, array $storeIds)
    {
        if (empty($storeIds)) {
            return true;
        }

        $metadata = $this->metadataPool->getMetadata(BlockInterface::class);
        $linkField = $metadata->getLinkField();

        $connection = $this->resourceConnection->getConnectionByName(
            $metadata->getEntityConnectionName()
        );
        $select = $connection->select()
            ->from(['cms_block_table' => $this->resourceConnection->getTableName('cms_block')])
            ->join(
                ['cms_block_store_table' => $this->resourceConnection->getTableName('cms_block_store')],
                'cms_block_table.' . $linkField . ' = cms_block_store_table.' . $linkField,
                []
            )
            ->where('cms_block_table.identifier = ?  ', $identifier)
            ->where('cms_block_store_table.store_id IN (?)', $storeIds);

        if ($connection->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}
