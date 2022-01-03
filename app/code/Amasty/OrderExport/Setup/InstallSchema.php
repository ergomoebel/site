<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Setup;

use Amasty\OrderExport\Setup\Module\Management as SetupModuleManagement;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    const OLD_MODULE_NAME = 'Amasty_Orderexport';

    /**
     * @var Operation\CreateProfileTable
     */
    private $createProfileTable;

    /**
     * @var Operation\CreateProfileEventTable
     */
    private $createProfileEventTable;

    /**
     * @var Operation\CreateConnectionTable
     */
    private $createConnectionTable;

    /**
     * @var SetupModuleManagement
     */
    private $setupModuleManagement;

    public function __construct(
        Operation\CreateProfileTable $createProfileTable,
        Operation\CreateProfileEventTable $createProfileEventTable,
        Operation\CreateConnectionTable $createConnectionTable,
        SetupModuleManagement $setupModuleManagement
    ) {
        $this->createProfileTable = $createProfileTable;
        $this->createProfileEventTable = $createProfileEventTable;
        $this->createConnectionTable = $createConnectionTable;
        $this->setupModuleManagement = $setupModuleManagement;
    }

    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $this->setupModuleManagement->assertIsDisabled(self::OLD_MODULE_NAME);
        $this->setupModuleManagement->deleteDbVersion(self::OLD_MODULE_NAME);

        $installer->startSetup();

        $this->createProfileTable->execute($installer);
        $this->createProfileEventTable->execute($installer);
        $this->createConnectionTable->execute($installer);

        $installer->endSetup();
    }
}
