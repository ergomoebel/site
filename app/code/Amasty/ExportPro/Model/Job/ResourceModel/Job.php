<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\Job\ResourceModel;

use Amasty\ExportPro\Model\Job\Job as JobModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Job extends AbstractDb
{
    const TABLE_NAME = 'amasty_export_cron_job';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, JobModel::JOB_ID);
    }
}
