<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Setup\Operation;

use Amasty\ExportPro\Model\History\History;
use Amasty\ExportPro\Model\History\ResourceModel\History as HistoryResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateExportHistoryTable
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(HistoryResource::TABLE_NAME))
            ->addColumn(
                History::HISTORY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                History::TYPE,
                Table::TYPE_TEXT,
                127,
                ['nullable' => true]
            )
            ->addColumn(
                History::JOB_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true]
            )
            ->addColumn(
                History::NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true]
            )
            ->addColumn(
                History::ENTITY_CODE,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                History::EXPORTED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                History::FINISHED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true]
            )
            ->addColumn(
                History::IDENTITY,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                History::STATUS,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false, 'default' => 0]
            )
            ->addColumn(
                History::LOG,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                History::IS_DELETED_FILE,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => false]
            );

        $installer->getConnection()->createTable($table);
    }
}
