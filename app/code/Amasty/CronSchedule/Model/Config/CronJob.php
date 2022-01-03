<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


declare(strict_types=1);

namespace Amasty\CronSchedule\Model\Config;

use Amasty\CronSchedule\Cron\RunJobs;
use Magento\Framework\DataObject;

class CronJob
{
    public function getConfig(string $jobType, string $externalId, string $expression): DataObject
    {
        $cronJobCode = 'amasty_cron_run_' . $jobType . '_' . $externalId;
        $method = 'amasty_cron_run_' . $jobType . '_external_id_' . $externalId;

        $jobConfig = [
            'name'     => $cronJobCode,
            'instance' => RunJobs::class,
            'method'   => $method,
            'schedule' => $expression
        ];

        return new DataObject($jobConfig);
    }

    public function matchMethods(string $name): array
    {
        if (preg_match('/^amasty_cron_run_([a-z0-9_]+)_external_id_([0-9]+)$/is', $name, $match)) {
            return $match;
        }

        return [];
    }
}
