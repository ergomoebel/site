<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Setup\Operation;

use Amasty\OrderExport\Model\Connection\Connection;
use Amasty\OrderExport\Model\Connection\ResourceModel\Connection as ConnectionResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateConnectionTable
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ConnectionResource::TABLE_NAME))
            ->addColumn(
                Connection::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                Connection::NAME,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Connection::TABLE_TO_JOIN,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Connection::PARENT_ENTITY,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Connection::ENTITY_CODE,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Connection::BASE_TABLE_KEY,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Connection::REFERENCED_TABLE_KEY,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            );

        $installer->getConnection()->createTable($table);
    }
}
