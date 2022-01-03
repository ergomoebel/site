<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Api\Data;

interface CronJobInterface
{
    /**
     * @return mixed
     */
    public function getJobId();

    /**
     * @param int $id
     * @return CronJobInterface
     */
    public function setJobId(int $id): CronJobInterface;

    /**
     * @return string|null
     */
    public function getConfig();

    /**
     * @param string|null $config
     * @return CronJobInterface
     */
    public function setConfig(?string $config): CronJobInterface;

    /**
     * @return \Amasty\ExportCore\Api\Config\ProfileConfigInterface|null
     */
    public function getProfileConfig(): ?\Amasty\ImportCore\Api\Config\ProfileConfigInterface;

    /**
     * @param \Amasty\ImportCore\Api\Config\ProfileConfigInterface|null $profileConfig
     *
     * @return \Amasty\ImportPro\Api\Data\CronJobInterface
     */
    public function setProfileConfig(
        ?\Amasty\ImportCore\Api\Config\ProfileConfigInterface $profileConfig
    ): CronJobInterface;

    /**
     * @return string|null
     */
    public function getTitle();

    /**
     * @param string $title
     * @return CronJobInterface
     */
    public function setTitle(string $title): CronJobInterface;

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     * @return CronJobInterface
     */
    public function setStatus(int $status): CronJobInterface;

    /**
     * @return string
     */
    public function getEntityCode();

    /**
     * @param string $entityCode
     * @return CronJobInterface
     */
    public function setEntityCode(string $entityCode): CronJobInterface;

    /**
     * @return \Amasty\CronSchedule\Api\Data\ScheduleInterface|null
     */
    public function getSchedule(): ?\Amasty\CronSchedule\Api\Data\ScheduleInterface;

    /**
     * @param \Amasty\CronSchedule\Api\Data\ScheduleInterface $schedule
     *
     * @return void
     */
    public function setSchedule(\Amasty\CronSchedule\Api\Data\ScheduleInterface $schedule): void;
}
