<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    const TABLE_NAMES = [
        \Amasty\ExportPro\Model\Job\ResourceModel\Job::TABLE_NAME,
        \Amasty\ExportPro\Model\History\ResourceModel\History::TABLE_NAME,
        \Amasty\ExportPro\Model\LastExportedId\ResourceModel\LastExportedId::TABLE_NAME
    ];

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        foreach (self::TABLE_NAMES as $tableName) {
            $setup->getConnection()->dropTable($setup->getTable($tableName));
        }

        $setup->endSetup();
    }
}
