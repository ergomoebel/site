<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


declare(strict_types=1);

namespace Amasty\ProductExport\Model\ProfileEvent\ResourceModel;

use Amasty\ProductExport\Model\ProfileEvent\ProfileEvent as ProfileEventModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProfileEvent extends AbstractDb
{
    const TABLE_NAME = 'amasty_product_export_profile_event';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ProfileEventModel::ID);
    }

    public function deleteByProfileId(int $profileId): bool
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['profile_id = ?' => $profileId]
        );

        return true;
    }
}
