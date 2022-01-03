<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\Attribute;

use Amasty\OrderExportEntity\Model\Indexer\Attribute as AttributeIndexer;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\Dimension;
use Psr\Log\LoggerInterface;

class IndexStructure implements IndexStructureInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var IndexScopeResolver
     */
    protected $indexScopeResolver;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AbstractAttribute[]
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var array
     */
    protected $indexes;

    /**
     * @var string[]
     */
    protected $staticColumns = [
        'order_item_id'
    ];

    public function __construct(
        ResourceConnection $resource,
        IndexScopeResolver $indexScopeResolver,
        EavConfig $eavConfig,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->indexScopeResolver = $indexScopeResolver;
        $this->eavConfig = $eavConfig;
        $this->logger = $logger;
    }

    /**
     * @param string $index
     * @param array $fields
     * @param Dimension[] $dimensions
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Exception
     */
    public function create($index, array $fields, array $dimensions = [])
    {
        $this->createAttributeIndex(
            $this->indexScopeResolver->resolve($index, $dimensions),
            $fields
        );
    }

    /**
     * Update index table
     *
     * @param string $index
     * @param array $fields
     * @param Dimension[] $dimensions
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Exception
     */
    public function update($index, array $fields, array $dimensions = [])
    {
        $this->updateAttributeIndex(
            $this->indexScopeResolver->resolve($index, $dimensions),
            $fields
        );
    }

    /**
     * @param string $index
     * @param Dimension[] $dimensions
     */
    public function delete($index, array $dimensions = [])
    {
        $tableName = $this->indexScopeResolver->resolve($index, $dimensions);

        $connection = $this->resource->getConnection();
        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    /**
     * @param array $attributeCodes
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAttributesFlatColumns(array $attributeCodes): array
    {
        if ($this->columns === null) {
            $this->columns = [];
            $columnsToPush = [];

            foreach ($this->getAttributes($attributeCodes) as $attribute) {
                /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
                $columns = $attribute->getFlatColumns();

                $attributeCode = $attribute->getAttributeCode();
                if ($attributeCode == 'status') {
                    $columns[$attributeCode] = [
                        'type' => Table::TYPE_INTEGER,
                        'unsigned' => false,
                        'nullable' => true,
                        'default' => null,
                        'extra' => null
                    ];
                } else {
                    if (isset($columns[$attributeCode])
                        && isset($columns[$attributeCode . '_value'])
                    ) {
                        $columns[$attributeCode] = $columns[$attributeCode . '_value'];
                        unset($columns[$attributeCode . '_value']);
                    }
                }

                if ($columns !== null) {
                    $columnsToPush[] = $columns;
                }
            }

            if (count($columnsToPush)) {
                $this->columns = array_merge(...$columnsToPush);
            }
        }

        return $this->columns;
    }

    /**
     * @param array $attributeCodes
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAttributesFlatIndexes(array $attributeCodes): array
    {
        if ($this->indexes === null) {
            $this->indexes = [];
            $indexesToPush = [];

            foreach ($this->getAttributes($attributeCodes) as $attribute) {
                /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
                $indexes = $attribute->getFlatIndexes();

                if ($indexes !== null) {
                    $indexesToPush[] = $indexes;
                }
            }

            if (count($indexesToPush)) {
                $this->indexes = array_merge(...$indexesToPush);
            }
        }

        return $this->indexes;
    }

    /**
     * @param string $index
     * @param array $attributesHash
     * @param Dimension[] $dimensions
     * @return array
     */
    public function getIndexedAttributes(string $index, array $attributesHash, array $dimensions = [])
    {
        $tableName = $this->indexScopeResolver->resolve($index, $dimensions);
        $columns = $this->resource->getConnection()
            ->describeTable($tableName);

        foreach ($attributesHash as $attributeId => $attributeCode) {
            if (!array_key_exists($attributeCode, $columns)) {
                unset($attributesHash[$attributeId]);
            }
        }

        return $attributesHash;
    }

    /**
     * @param string $tableName
     * @param array $fields
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Exception
     */
    protected function createAttributeIndex(string $tableName, array $fields)
    {
        $connection = $this->resource->getConnection();

        $table = $connection->newTable($tableName)->addColumn(
            'order_item_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Order Item Id'
        )->addIndex(
            $this->resource->getIdxName(
                AttributeIndexer::INDEXER_ID,
                ['order_item_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['order_item_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $this->resource->getFkName(
                AttributeIndexer::INDEXER_ID,
                'order_item_id',
                'sales_order_item',
                'item_id'
            ),
            'order_item_id',
            $this->resource->getTableName('sales_order_item'),
            'item_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Order Export Attribute Index'
        );
        $connection->createTable($table);

        $this->addFlatColumns($tableName, $this->getAttributesFlatColumns($fields));
    }

    /**
     * @param string $tableName
     * @param array $fields
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function updateAttributeIndex(string $tableName, array $fields)
    {
        $columnsInTable = array_keys($this->resource->getConnection()
            ->describeTable($tableName));
        $canonicalColumns = array_merge($this->staticColumns, $fields);
        $columnsToDelete = array_diff($columnsInTable, $canonicalColumns);
        $fieldsToAdd = array_diff($fields, $columnsInTable);

        foreach ($columnsToDelete as $columnCode) {
            $this->resource->getConnection()->dropColumn($tableName, $columnCode);
        }

        if (count($fieldsToAdd)) {
            $this->addFlatColumns($tableName, $this->getAttributesFlatColumns($fieldsToAdd));
        }
    }

    /**
     * @param string $tableName
     * @param array $attributesFlatColumns
     */
    protected function addFlatColumns(string $tableName, array $attributesFlatColumns)
    {
        foreach ($attributesFlatColumns as $fieldName => $fieldProp) {
            $columnDefinition = [
                'type' => $fieldProp['type'],
                'length' => $fieldProp['length'] ?? null,
                'nullable' => (bool)($fieldProp['nullable'] ?? false),
                'unsigned' => (bool)($fieldProp['unsigned'] ?? false),
                'default' => (bool)($fieldProp['default'] ?? false),
                'comment' => $fieldProp['comment'] ?? $fieldName
            ];

            $this->resource->getConnection()
                ->addColumn($tableName, $fieldName, $columnDefinition);
        }
    }

    /**
     * @param string $tableName
     * @param array $attributesFlatIndexes
     */
    protected function addFlatIndexes(string $tableName, array $attributesFlatIndexes)
    {
        foreach ($attributesFlatIndexes as $indexProp) {
            $indexName = $this->resource->getIdxName($tableName, $indexProp['fields'], $indexProp['type']);
            $this->resource->getConnection()->addIndex(
                $tableName,
                $indexName,
                $indexProp['fields'],
                strtolower($indexProp['type'])
            );
        }
    }

    /**
     * Get attributes
     *
     * @param array $attributeCodes
     * @return AbstractAttribute[]|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAttributes(array $attributeCodes): ?array
    {
        if ($this->attributes === null) {
            $this->attributes = [];

            foreach ($attributeCodes as $attributeCode) {
                $attribute = $this->eavConfig->getAttribute(
                    Product::ENTITY,
                    $attributeCode
                );

                if ($attribute->getId()) {
                    try {
                        // Check if exists source and backend model.
                        // To prevent exception when some module was disabled
                        $attribute->usesSource() && $attribute->getSource();
                        $attribute->getBackend();

                        $this->attributes[$attributeCode] = $attribute;
                    } catch (\Exception $e) {
                        $this->logger->critical($e);
                    }

                    $this->attributes[$attributeCode] = $attribute;
                }
            }
        }

        return $this->attributes;
    }
}
