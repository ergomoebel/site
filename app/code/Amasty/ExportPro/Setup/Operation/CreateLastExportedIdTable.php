<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Setup\Operation;

use Amasty\ExportPro\Model\LastExportedId\LastExportedId;
use Amasty\ExportPro\Model\LastExportedId\ResourceModel\LastExportedId as LastExportedIdResource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateLastExportedIdTable
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(LastExportedIdResource::TABLE_NAME))
            ->addColumn(
                LastExportedId::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                LastExportedId::TYPE,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                LastExportedId::EXTERNAL_ID,
                Table::TYPE_INTEGER,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                LastExportedId::LAST_EXPORTED_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false]
            )
            ->addIndex(
                $installer->getIdxName(
                    LastExportedIdResource::TABLE_NAME,
                    [LastExportedId::ID],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [LastExportedId::ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );

        $installer->getConnection()->createTable($table);
    }
}
