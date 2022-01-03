<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


declare(strict_types=1);

namespace Amasty\CustomerImport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateProfileTable
     */
    private $createProfileTable;

    public function __construct(
        Operation\CreateProfileTable $createProfileTable
    ) {
        $this->createProfileTable = $createProfileTable;
    }

    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        $this->createProfileTable->execute($installer);

        $installer->endSetup();
    }
}
