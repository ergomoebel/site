<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\LastExportedId\ResourceModel;

use Amasty\ExportPro\Model\LastExportedId\LastExportedId as LastExportedIdModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LastExportedId extends AbstractDb
{
    const TABLE_NAME = 'amasty_export_last_exported_id';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, LastExportedIdModel::ID);
    }
}
