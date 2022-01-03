<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateProfileTable
     */
    private $createProfileTable;

    /**
     * @var Operation\CreateEntityLogTable
     */
    private $createEntityLogTable;

    public function __construct(
        Operation\CreateProfileTable $createProfileTable,
        Operation\CreateEntityLogTable $createEntityLogTable
    ) {
        $this->createProfileTable = $createProfileTable;
        $this->createEntityLogTable = $createEntityLogTable;
    }

    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        $this->createProfileTable->execute($installer);
        $this->createEntityLogTable->execute($installer);

        $installer->endSetup();
    }
}
