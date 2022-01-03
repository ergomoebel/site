<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\CustomOption;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\Dimension;

/**
 * Custom options indexer handler
 */
class IndexerHandler implements IndexerInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var Batch
     */
    private $batch;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var IndexScopeResolver
     */
    private $indexScopeResolver;

    /**
     * @var IndexStructure
     */
    private $indexStructure;

    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private $isIndexTableExistsCheckDone = false;

    public function __construct(
        ResourceConnection $resource,
        Batch $batch,
        IndexScopeResolver $indexScopeResolver,
        IndexStructure $indexStructure,
        array $data,
        $batchSize = 200
    ) {
        $this->resource = $resource;
        $this->batch = $batch;
        $this->indexScopeResolver = $indexScopeResolver;
        $this->indexStructure = $indexStructure;
        $this->data = $data;
        $this->batchSize = $batchSize;
    }

    public function saveIndex($dimensions, \Traversable $documents)
    {
        $this->ensureIndexTableExists($dimensions);
        foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
            if (!empty($batchDocuments)) {
                $this->resource->getConnection()
                    ->insertMultiple(
                        $this->getIndexTableName($dimensions),
                        $this->prepareDocumentsToInset($batchDocuments)
                    );
            }
        }
    }

    public function deleteIndex($dimensions, \Traversable $documents)
    {
        $this->ensureIndexTableExists($dimensions);
        foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
            $this->resource->getConnection()
                ->delete(
                    $this->getIndexTableName($dimensions),
                    ['order_item_id in (?)' => $batchDocuments]
                );
        }
    }

    public function cleanIndex($dimensions)
    {
        $this->ensureIndexTableExists($dimensions);
        $this->resource->getConnection()
            ->truncateTable($this->getIndexTableName($dimensions));
    }

    public function isAvailable($dimensions = []): bool
    {
        return true;
    }

    /**
     * Get index tale name
     *
     * @param Dimension[] $dimensions
     * @return string
     */
    private function getIndexTableName($dimensions)
    {
        return $this->indexScopeResolver->resolve($this->getIndexName(), $dimensions);
    }

    /**
     * Ensures that index table exists
     *
     * @param Dimension[] $dimensions
     * @return void
     */
    private function ensureIndexTableExists($dimensions)
    {
        if (!$this->isIndexTableExistsCheckDone) {
            $tableName = $this->getIndexTableName($dimensions);
            if (!$this->resource->getConnection()->isTableExists($tableName)) {
                $this->indexStructure->create($this->getIndexName(), [], $dimensions);
            }

            $this->isIndexTableExistsCheckDone = true;
        }
    }

    /**
     * Get index name
     *
     * @return string
     */
    private function getIndexName()
    {
        return $this->data['indexer_id'];
    }

    /**
     * Prepare documents to insert
     *
     * @param array $documents
     * @return array
     */
    private function prepareDocumentsToInset(array $documents)
    {
        $documentsToInsert = [];
        foreach ($documents as $entityDocuments) {

            /**
             * Collect entity documents callback
             *
             * @param array $entityDoc
             * @return void
             */
            $collectEntityDocsCallback = function (array $entityDoc) use (&$documentsToInsert) {
                $documentsToInsert[] = $entityDoc;
            };
            array_walk($entityDocuments, $collectEntityDocsCallback);
        }
        return $documentsToInsert;
    }
}
