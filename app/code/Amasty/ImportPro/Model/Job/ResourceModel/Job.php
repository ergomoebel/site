<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Model\Job\ResourceModel;

use Amasty\ImportPro\Model\Job\Job as JobModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Job extends AbstractDb
{
    const TABLE_NAME = 'amasty_import_cron_job';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, JobModel::JOB_ID);
    }
}
