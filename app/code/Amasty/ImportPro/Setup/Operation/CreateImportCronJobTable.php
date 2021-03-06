<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Setup\Operation;

use Amasty\ImportPro\Model\Job\Job;
use Amasty\ImportPro\Model\Job\ResourceModel\Job as JobResource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateImportCronJobTable
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(JobResource::TABLE_NAME))
            ->addColumn(
                Job::JOB_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                Job::TITLE,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Job::ENTITY_CODE,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Job::STATUS,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                Job::GROUP,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                Job::CONFIG,
                Table::TYPE_BLOB,
                Table::MAX_TEXT_SIZE,
                ['nullable' => false]
            )
            ->addIndex(
                $installer->getIdxName(
                    JobResource::TABLE_NAME,
                    [Job::JOB_ID],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [Job::JOB_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );

        $installer->getConnection()->createTable($table);
    }
}
