<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


namespace Amasty\CronSchedule\Plugin\Cron\Model;

use Amasty\CronSchedule\Model\Config\CronJob;
use Amasty\CronSchedule\Model\Schedule\ResourceModel\CollectionFactory;
use Amasty\CronSchedule\Model\Schedule\Schedule;

class ConfigPlugin
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CronJob
     */
    private $cronJob;

    public function __construct(
        CollectionFactory $collectionFactory,
        CronJob $cronJob
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->cronJob = $cronJob;
    }

    public function afterGetJobs(\Magento\Cron\Model\Config $subject, array $result)
    {
        $schedules = $this->collectionFactory->create()->getItems();
        /** @var Schedule $schedule */
        foreach ($schedules as $schedule) {
            if ($schedule->getExpression() && $schedule->isEnabled()) {
                $cronConfig = $this->cronJob->getConfig(
                    $schedule->getJobType(),
                    $schedule->getExternalId(),
                    $schedule->getExpression()
                );

                $result['amasty_cron_schedule'][$cronConfig->getName()] = $cronConfig->getData();
            }
        }

        return $result;
    }
}
