<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateExportCronJobTable
     */
    private $createExportCronJobTable;

    /**
     * @var Operation\CreateExportHistoryTable
     */
    private $createExportHistoryTable;

    /**
     * @var Operation\CreateLastExportedIdTable
     */
    private $createLastExportedIdTable;

    public function __construct(
        Operation\CreateExportCronJobTable $createExportCronJobTable,
        Operation\CreateExportHistoryTable $createExportHistoryTable,
        Operation\CreateLastExportedIdTable $createLastExportedIdTable
    ) {
        $this->createExportCronJobTable = $createExportCronJobTable;
        $this->createExportHistoryTable = $createExportHistoryTable;
        $this->createLastExportedIdTable = $createLastExportedIdTable;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        $this->createExportCronJobTable->execute($installer);
        $this->createExportHistoryTable->execute($installer);
        $this->createLastExportedIdTable->execute($installer);

        $installer->endSetup();
    }
}
