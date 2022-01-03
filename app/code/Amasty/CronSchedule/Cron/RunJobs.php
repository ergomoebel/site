<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


declare(strict_types=1);

namespace Amasty\CronSchedule\Cron;

use Amasty\CronSchedule\Model\Config\CronJob;
use Amasty\CronSchedule\Model\Schedule\Schedule;
use Magento\Framework\Event\ManagerInterface;

class RunJobs
{
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var CronJob
     */
    private $cronJob;

    public function __construct(
        ManagerInterface $eventManager,
        CronJob $cronJob
    ) {
        $this->eventManager = $eventManager;
        $this->cronJob = $cronJob;
    }

    public function __call($name, $arguments)
    {
        if ($match = $this->cronJob->matchMethods($name)) {
            $this->eventManager->dispatch(
                Schedule::EVENT_NAME . $match[1],
                [
                    'external_id' => $match[2]
                ]
            );
        }
    }
}
