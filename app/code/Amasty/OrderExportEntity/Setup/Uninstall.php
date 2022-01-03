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
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    const TABLE_NAMES = [
        \Amasty\OrderExportEntity\Model\ResourceModel\Attribute::TABLE_NAME,
        \Amasty\OrderExportEntity\Model\Indexer\Attribute::INDEXER_ID
    ];

    public function uninstall(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        foreach (self::TABLE_NAMES as $tableName) {
            $installer->getConnection()->dropTable($installer->getTable($tableName));
        }

        $installer->endSetup();
    }
}
