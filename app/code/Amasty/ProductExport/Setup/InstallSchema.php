<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


declare(strict_types=1);

namespace Amasty\ProductExport\Setup;

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
     * @var Operation\CreateProfileEventTable
     */
    private $createProfileEventTable;

    /**
     * @var Operation\CreateConnectionTable
     */
    private $createConnectionTable;

    public function __construct(
        Operation\CreateProfileTable $createProfileTable,
        Operation\CreateProfileEventTable $createProfileEventTable,
        Operation\CreateConnectionTable $createConnectionTable
    ) {
        $this->createProfileTable = $createProfileTable;
        $this->createProfileEventTable = $createProfileEventTable;
        $this->createConnectionTable = $createConnectionTable;
    }

    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        $this->createProfileTable->execute($installer);
        $this->createProfileEventTable->execute($installer);
        $this->createConnectionTable->execute($installer);

        $installer->endSetup();
    }
}
