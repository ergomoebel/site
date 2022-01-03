<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateImportCronJobTable
     */
    private $createImportCronJobTable;

    /**
     * @var Operation\CreateImportHistoryTable
     */
    private $createImportHistoryTable;

    public function __construct(
        Operation\CreateImportCronJobTable $createImportCronJobTable,
        Operation\CreateImportHistoryTable $createImportHistoryTable
    ) {
        $this->createImportCronJobTable = $createImportCronJobTable;
        $this->createImportHistoryTable = $createImportHistoryTable;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        $this->createImportCronJobTable->execute($installer);
        $this->createImportHistoryTable->execute($installer);

        $installer->endSetup();
    }
}
