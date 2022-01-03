<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Setup\Operation;

use Amasty\ProductImport\Model\EntityLog\EntityLog;
use Amasty\ProductImport\Model\EntityLog\ResourceModel\EntityLog as EntityLogResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateEntityLogTable
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(EntityLogResource::TABLE_NAME))
            ->addColumn(
                EntityLog::ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ]
            )->addColumn(
                EntityLog::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )->addColumn(
                EntityLog::PROCESS_IDENTITY,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )->addColumn(
                EntityLog::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ]
            );

        $installer->getConnection()->createTable($table);
    }
}
