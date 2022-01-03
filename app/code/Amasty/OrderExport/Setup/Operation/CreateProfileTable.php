<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Setup\Operation;

use Amasty\OrderExport\Model\Profile\Profile;
use Amasty\OrderExport\Model\Profile\ResourceModel\Profile as ProfileResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateProfileTable
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ProfileResource::TABLE_NAME))
            ->addColumn(
                Profile::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                Profile::NAME,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Profile::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false, 'default' => Table::TIMESTAMP_INIT
                ]
            )
            ->addColumn(
                Profile::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE
                ]
            )
            ->addColumn(
                Profile::EXPORTED_AT,
                Table::TYPE_DATETIME,
                null,
                [
                    'nullable' => false
                ]
            )
            ->addColumn(
                Profile::SERIALIZED_CONFIG,
                Table::TYPE_BLOB,
                Table::MAX_TEXT_SIZE,
                ['nullable' => false]
            )->addColumn(
                Profile::FILE_FORMAT,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )->addColumn(
                Profile::EXECUTION_TYPE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )->addColumn(
                Profile::ORDER_ACTIONS,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false]
            );

        $installer->getConnection()->createTable($table);
    }
}
