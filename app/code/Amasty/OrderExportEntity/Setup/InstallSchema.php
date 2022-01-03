<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateAttributeTable
     */
    private $createAttributeTable;

    public function __construct(
        Operation\CreateAttributeTable $createAttributeTable
    ) {
        $this->createAttributeTable = $createAttributeTable;
    }

    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        $this->createAttributeTable->execute($installer);

        $installer->endSetup();
    }
}
