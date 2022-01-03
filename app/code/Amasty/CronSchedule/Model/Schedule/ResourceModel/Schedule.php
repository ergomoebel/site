<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


declare(strict_types=1);

namespace Amasty\CronSchedule\Model\Schedule\ResourceModel;

use Amasty\CronSchedule\Model\Schedule\Schedule as ScheduleModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Schedule extends AbstractDb
{
    const TABLE_NAME = 'amasty_cron_schedule';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ScheduleModel::SCHEDULE_ID);
    }
}
