<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Setup\Operation;

use Amasty\CustomerExport\Model\Profile\Profile;
use Amasty\CustomerExport\Model\Profile\ResourceModel\Profile as ProfileResource;
use Amasty\CustomerExport\Model\ProfileEvent\ProfileEvent;
use Amasty\CustomerExport\Model\ProfileEvent\ResourceModel\ProfileEvent as ProfileEventResource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateProfileEventTable
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ProfileEventResource::TABLE_NAME))
            ->addColumn(
                ProfileEvent::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                ProfileEvent::PROFILE_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                ProfileEvent::EVENT_NAME,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addIndex(
                $installer->getIdxName(ProfileEventResource::TABLE_NAME, [ProfileEvent::PROFILE_ID]),
                [ProfileEvent::PROFILE_ID]
            )
            ->addIndex(
                $installer->getIdxName(
                    ProfileEventResource::TABLE_NAME,
                    [ProfileEvent::PROFILE_ID, ProfileEvent::EVENT_NAME],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [ProfileEvent::PROFILE_ID, ProfileEvent::EVENT_NAME],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName(
                    ProfileEventResource::TABLE_NAME,
                    ProfileEvent::PROFILE_ID,
                    ProfileResource::TABLE_NAME,
                    Profile::ID
                ),
                ProfileEvent::PROFILE_ID,
                $installer->getTable(ProfileResource::TABLE_NAME),
                Profile::ID,
                Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($table);
    }
}
