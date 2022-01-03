<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Api\Data;

interface CronJobInterface
{
    /**
     * @return int|null
     */
    public function getJobId(): ?int;

    /**
     * @param int $id
     *
     * @return CronJobInterface
     */
    public function setJobId(int $id): CronJobInterface;

    /**
     * @return string|null
     */
    public function getConfig(): ?string;

    /**
     * @param string|null $config
     *
     * @return CronJobInterface
     */
    public function setConfig(?string $config): CronJobInterface;

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @param string $title
     *
     * @return CronJobInterface
     */
    public function setTitle(string $title): CronJobInterface;

    /**
     * @return int|null
     */
    public function getIndexedStatus(): ?int;

    /**
     * @param int $status
     *
     * @return CronJobInterface
     */
    public function setIndexedStatus(int $status): CronJobInterface;

    /**
     * @return string|null
     */
    public function getEntityCode(): ?string;

    /**
     * @param string $entityCode
     *
     * @return CronJobInterface
     */
    public function setEntityCode(string $entityCode): CronJobInterface;

    /**
     * @return \Amasty\ExportCore\Api\Config\ProfileConfigInterface|null
     */
    public function getProfileConfig(): ?\Amasty\ExportCore\Api\Config\ProfileConfigInterface;

    /**
     * @param \Amasty\ExportCore\Api\Config\ProfileConfigInterface|null $profileConfig
     *
     * @return \Amasty\ExportPro\Api\Data\CronJobInterface
     */
    public function setProfileConfig(
        ?\Amasty\ExportCore\Api\Config\ProfileConfigInterface $profileConfig
    ): CronJobInterface;

    /**
     * @return \Amasty\CronSchedule\Api\Data\ScheduleInterface|null
     */
    public function getSchedule(): ?\Amasty\CronSchedule\Api\Data\ScheduleInterface;

    /**
     * @param \Amasty\CronSchedule\Api\Data\ScheduleInterface $schedule
     *
     * @return \Amasty\ExportPro\Api\Data\CronJobInterface
     */
    public function setSchedule(
        \Amasty\CronSchedule\Api\Data\ScheduleInterface $schedule
    ): CronJobInterface;
}
