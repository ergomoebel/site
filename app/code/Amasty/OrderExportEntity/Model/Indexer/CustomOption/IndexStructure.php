<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\CustomOption;

use Amasty\OrderExportEntity\Model\Indexer\CustomOption as CustomOptionIndexer;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;

/**
 * Custom options indexer structure
 */
class IndexStructure implements IndexStructureInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var IndexScopeResolver
     */
    private $indexScopeResolver;

    /**
     * @var array
     */
    private $fields = [
        'order_item_id' => [
            'type' => Table::TYPE_INTEGER,
            'size' => 10
        ],
        'option_title' => [
            'type' => Table::TYPE_TEXT,
            'size' => 255
        ],
        'option_value' => [
            'type' => Table::TYPE_TEXT,
            'size' => 65536
        ],
        'price' => [
            'type' => Table::TYPE_DECIMAL,
            'size' => '12,4'
        ],
        'sku' => [
            'type' => Table::TYPE_TEXT,
            'size' => 64
        ]
    ];

    public function __construct(
        ResourceConnection $resource,
        IndexScopeResolver $indexScopeResolver
    ) {
        $this->resource = $resource;
        $this->indexScopeResolver = $indexScopeResolver;
    }

    public function delete($index, array $dimensions = [])
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->indexScopeResolver->resolve($index, $dimensions);
        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    public function create($index, array $fields, array $dimensions = [])
    {
        $connection = $this->resource->getConnection();
        $table = $connection->newTable($this->indexScopeResolver->resolve($index, $dimensions));
        $table->addColumn(
            'row_id',
            Table::TYPE_BIGINT,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Index Row Id'
        );

        $fields = array_merge($this->fields, $fields);
        foreach ($fields as $fieldName => $fieldDefinition) {
            $columnOptions = [];
            if ($fieldName == 'order_item_id') {
                $columnOptions = [
                    'unsigned' => true,
                    'nullable' => false
                ];
            }

            $table->addColumn(
                $fieldName,
                isset($fieldDefinition['type']) ? $fieldDefinition['type'] : Table::TYPE_TEXT,
                isset($fieldDefinition['size']) ? $fieldDefinition['size'] : 255,
                $columnOptions
            );
        }

        $table->addForeignKey(
            $this->resource->getFkName(
                CustomOptionIndexer::INDEXER_ID,
                'order_item_id',
                'sales_order_item',
                'item_id'
            ),
            'order_item_id',
            $this->resource->getTableName('sales_order_item'),
            'item_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Order Export Custom Option Index'
        );

        $connection->createTable($table);
    }
}
