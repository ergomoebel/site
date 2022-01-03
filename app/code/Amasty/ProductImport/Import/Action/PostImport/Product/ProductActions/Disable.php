<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */

declare(strict_types=1);

namespace Amasty\ProductImport\Import\Action\PostImport\Product\ProductActions;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ProductImport\Import\Action\Import\Product\ProductActions\AbstractAction;
use Amasty\ProductImport\Model\EntityLog\ResourceModel\EntityLog as EntityLogResource;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Indexer\Product\Eav\Processor as EavProcessor;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor as StockProcessor;
use Magento\CatalogRule\Model\Indexer\Product\ProductRuleProcessor;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Shell;
use Symfony\Component\Process\PhpExecutableFinder;

class Disable extends AbstractAction
{
    const ATTRIBUTE_CODE = 'status';

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var PhpExecutableFinder
     */
    private $phpExecutableFinder;

    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        Shell $shell,
        PhpExecutableFinder $phpExecutableFinder
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
        $this->shell = $shell;
        $this->phpExecutableFinder = $phpExecutableFinder;
    }

    /**
     * @inheritdoc
     */
    public function execute(ImportProcessInterface $importProcess): void
    {
        $importProcess->addInfoMessage(__('Started disabling the products not added to the file.')->render());
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $attribute = $this->attributeRepository->get(
            $metadata->getEavEntityType(),
            self::ATTRIBUTE_CODE
        );

        $connection = $this->resourceConnection->getConnection($metadata->getEntityConnectionName());
        $linkFieldExpr = new \Zend_Db_Expr(
            sprintf(
                '(%s)',
                $this->getLinkFieldSelect(
                    $connection,
                    $metadata,
                    $importProcess->getIdentity()
                )->assemble()
            )
        );
        $connection->update(
            $attribute->getBackend()->getTable(),
            ['value' => Status::STATUS_DISABLED],
            [
                'attribute_id = ?' => $attribute->getAttributeId(),
                $metadata->getLinkField() . ' NOT IN (?)' => $linkFieldExpr
            ]
        );
        $phpPath = $this->phpExecutableFinder->find() ?: 'php';

        $this->shell->execute(
            $phpPath . ' %s indexer:reindex %s %s %s %s > /dev/null &',
            [
                BP . '/bin/magento',
                StockProcessor::INDEXER_ID,
                EavProcessor::INDEXER_ID,
                Fulltext::INDEXER_ID,
                ProductRuleProcessor::INDEXER_ID

            ]
        );

        $importProcess->addInfoMessage(__('The process of products disabling is finished.')->render());
    }

    /**
     * @param AdapterInterface $connection
     * @param EntityMetadataInterface $metadata
     * @param string $identity
     * @return Select
     */
    private function getLinkFieldSelect(
        AdapterInterface $connection,
        EntityMetadataInterface $metadata,
        string $identity
    ): Select {
        $tableName = $this->resourceConnection->getTableName(
            EntityLogResource::TABLE_NAME,
            $metadata->getEntityConnectionName()
        );

        return $connection->select()
            ->from($tableName, ['entity_id'])
            ->where('process_identity = ?', $identity);
    }
}
