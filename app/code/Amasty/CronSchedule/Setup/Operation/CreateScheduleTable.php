<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


declare(strict_types=1);

namespace Amasty\CronSchedule\Setup\Operation;

use Amasty\CronSchedule\Model\Schedule\Schedule;
use Amasty\CronSchedule\Model\Schedule\ResourceModel\Schedule as ScheduleResource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateScheduleTable
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ScheduleResource::TABLE_NAME))
            ->addColumn(
                Schedule::SCHEDULE_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                Schedule::EXTERNAL_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                Schedule::JOB_TYPE,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Schedule::ENABLED,
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                Schedule::EXPRESSION,
                Table::TYPE_TEXT,
                127,
                ['nullable' => false]
            )
            ->addColumn(
                Schedule::SERIALIZED_EXTENSION_ATTRIBUTES,
                Table::TYPE_TEXT,
                null
            )
            ->addIndex(
                $installer->getIdxName(
                    ScheduleResource::TABLE_NAME,
                    [Schedule::SCHEDULE_ID],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [Schedule::SCHEDULE_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );

        $installer->getConnection()->createTable($table);
    }
}
