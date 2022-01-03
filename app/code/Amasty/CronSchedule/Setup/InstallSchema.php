<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


declare(strict_types=1);

namespace Amasty\CronSchedule\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateScheduleTable
     */
    private $createScheduleTable;

    public function __construct(
        Operation\CreateScheduleTable $createScheduleTable
    ) {
        $this->createScheduleTable = $createScheduleTable;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        $this->createScheduleTable->execute($installer);

        $installer->endSetup();
    }
}
