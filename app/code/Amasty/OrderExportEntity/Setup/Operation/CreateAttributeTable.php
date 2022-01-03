<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Setup\Operation;

use Amasty\OrderExportEntity\Model\ResourceModel\Attribute as AttributeResource;
use Amasty\OrderExportEntity\Model\Indexer\Attribute as AttributeIndexer;
use Amasty\OrderExportEntity\Model\Attribute;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateAttributeTable
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable(AttributeResource::TABLE_NAME)
        )->addColumn(
            Attribute::ATTRIBUTE_ID,
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'attribute_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true, 'default' => '0'],
            'Attribute Id'
        )->addColumn(
            'attribute_code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Attribute Code'
        )->addColumn(
            'frontend_label',
            Table::TYPE_TEXT,
            255,
            [],
            'Frontend Label'
        )->addIndex(
            $installer->getIdxName(
                AttributeResource::TABLE_NAME,
                ['attribute_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['attribute_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(AttributeResource::TABLE_NAME, 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $installer->getTable('eav_attribute'),
            'attribute_id',
            Table::ACTION_SET_NULL
        )->setComment(
            'Amasty Order Export Attribute'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable(AttributeIndexer::INDEXER_ID)
        )->addColumn(
            'order_item_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Item Id'
        )->addIndex(
            $installer->getIdxName(
                AttributeIndexer::INDEXER_ID,
                ['order_item_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['order_item_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(AttributeIndexer::INDEXER_ID, 'order_item_id', 'sales_order_item', 'item_id'),
            'order_item_id',
            $installer->getTable('sales_order_item'),
            'item_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Order Export Attribute Index'
        );
        $installer->getConnection()->createTable($table);
    }
}
