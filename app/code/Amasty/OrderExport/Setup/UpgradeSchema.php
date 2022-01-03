<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Setup;

use Amasty\OrderExport\Model\Connection\ResourceModel\Connection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->upgradeTo101($setup);
        }

        $setup->endSetup();
    }

    protected function upgradeTo101(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->dropColumn($setup->getTable(Connection::TABLE_NAME), 'child_entity');
        $setup->getConnection()
            ->dropColumn($setup->getTable(Connection::TABLE_NAME), 'sub_entity_field_name');
    }
}
