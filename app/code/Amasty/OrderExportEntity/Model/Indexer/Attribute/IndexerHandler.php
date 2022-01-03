<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\Attribute;

use Amasty\OrderExportEntity\Model\ResourceModel\Attribute\Collection as AttributeCollection;
use Amasty\OrderExportEntity\Model\ResourceModel\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Convert\DataObject as ObjectConverter;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\Dimension;

class IndexerHandler implements IndexerInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var Batch
     */
    protected $batch;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var int
     */
    protected $batchSize;

    /**
     * @var IndexScopeResolver
     */
    protected $indexScopeResolver;

    /**
     * @var IndexStructure
     */
    protected $indexStructure;

    /**
     * @var AttributeCollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var ObjectConverter
     */
    protected $objectConverter;

    /**
     * @var AttributeCollection
     */
    protected $attributeCollection;

    /**
     * @var array
     */
    protected $attributesHash = [];

    public function __construct(
        ResourceConnection $resource,
        IndexStructure $indexStructure,
        Config $eavConfig,
        Batch $batch,
        IndexScopeResolver $indexScopeResolver,
        AttributeCollectionFactory $attributeCollectionFactory,
        ObjectConverter $objectConverter,
        array $data,
        $batchSize = 200
    ) {
        $this->indexScopeResolver = $indexScopeResolver;
        $this->resource = $resource;
        $this->batch = $batch;
        $this->eavConfig = $eavConfig;
        $this->data = $data;
        $this->fields = [];
        $this->batchSize = $batchSize;
        $this->indexStructure = $indexStructure;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->objectConverter = $objectConverter;
    }

    /**
     * @param Dimension[] $dimensions
     * @param \Traversable $documents
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
            $this->insertDocuments($batchDocuments, $dimensions);
        }
    }

    /**
     * @param Dimension[] $dimensions
     * @param \Traversable $documents
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
            $this->resource->getConnection()
                ->delete($this->getTableName($dimensions), ['item_id in (?)' => $batchDocuments]);
        }
    }

    /**
     * @param Dimension[] $dimensions
     * @return IndexerInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Exception
     */
    public function cleanIndex($dimensions)
    {
        $this->indexStructure->delete($this->getIndexName());
        $this->indexStructure->create(
            $this->getIndexName(),
            $this->objectConverter->toOptionHash(
                $this->getAttributeCollection()->getItems(),
                'attribute_id',
                'attribute_code'
            ),
            $dimensions
        );
    }

    /**
     * @param Dimension[] $dimensions
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Exception
     */
    public function updateIndex($dimensions)
    {
        $this->indexStructure->update(
            $this->getIndexName(),
            $this->objectConverter->toOptionHash(
                $this->getAttributeCollection()->getItems(),
                'attribute_id',
                'attribute_code'
            ),
            $dimensions
        );
    }

    /**
     * @param array $dimensions
     * @return bool
     */
    public function isAvailable($dimensions = []): bool
    {
        return true;
    }

    /**
     * @return AttributeCollection
     */
    protected function getAttributeCollection()
    {
        if ($this->attributeCollection === null) {
            $this->attributeCollection = $this->attributeCollectionFactory->create()
                ->addFieldToFilter('attribute_id', ['notnull' => true]);
        }

        return $this->attributeCollection;
    }

    /**
     * @param Dimension[] $dimensions
     * @return string
     */
    private function getTableName($dimensions)
    {
        return $this->indexScopeResolver->resolve($this->getIndexName(), $dimensions);
    }

    /**
     * @return string
     */
    private function getIndexName()
    {
        return $this->data['indexer_id'];
    }

    private function insertDocuments(array $documents, array $dimensions)
    {
        $attributesHash = $this->getAttributeHash();
        $documents = $this->prepareFields($documents, $attributesHash);

        if (empty($documents)) {
            return;
        }

        $this->resource->getConnection()->insertOnDuplicate(
            $this->getTableName($dimensions),
            $documents,
            $attributesHash
        );
    }

    protected function prepareFields(array $documents, array $attributes)
    {
        $insertDocuments = [];

        foreach ($documents as $entityId => $document) {
            $attributesData = [];

            foreach ($attributes as $attributeId => $attributeCode) {
                if (array_key_exists($attributeCode, $document)) {
                    $attributesData[$attributeCode] = $document[$attributeCode];
                } else {
                    $attributesData[$attributeCode] = null;
                }
            }

            $attributesData['order_item_id'] = $entityId;
            $insertDocuments[$entityId] = $attributesData;
        }

        return $insertDocuments;
    }

    /**
     * @param Dimension[] $dimensions
     * @return array
     */
    public function getIndexedAttributesHash(array $dimensions)
    {
        return $this->indexStructure->getIndexedAttributes(
            $this->getIndexName(),
            $this->objectConverter->toOptionHash(
                $this->getAttributeCollection()->getItems(),
                'attribute_id',
                'attribute_code'
            ),
            $dimensions
        );
    }

    public function setAttributeHash(array $attributesHash): IndexerHandler
    {
        $this->attributesHash = $attributesHash;

        return $this;
    }

    public function getAttributeHash(): array
    {
        return $this->attributesHash;
    }
}
