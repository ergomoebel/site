<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\Job;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportPro\Api\Data\CronJobInterface;
use Magento\Framework\Model\AbstractModel;

class Job extends AbstractModel implements CronJobInterface
{
    const JOB_ID = 'job_id';
    const TITLE = 'title';
    const CONFIG = 'config';
    const ENTITY_CODE = 'entity_code';
    const JOB_EXPORT_TYPE = 'export';
    const PROFILE_CONFIG = 'profile_config';
    const SCHEDULE = 'schedule';
    const INDEXED_STATUS = 'indexed_status';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Job::class);
        $this->setIdFieldName(self::JOB_ID);
    }

    public function getJobId(): ?int
    {
        return $this->hasData(self::JOB_ID) ? (int)$this->getData(self::JOB_ID) : null;
    }

    public function setJobId(int $id): CronJobInterface
    {
        return $this->setData(self::JOB_ID, $id);
    }

    public function getConfig(): ?string
    {
        return $this->hasData(self::CONFIG) ? $this->getData(self::CONFIG) : null;
    }

    public function setConfig(?string $config): CronJobInterface
    {
        return $this->setData(self::CONFIG, $config);
    }

    public function getTitle(): ?string
    {
        return $this->hasData(self::TITLE) ? $this->getData(self::TITLE) : null;
    }

    public function setTitle(string $title): CronJobInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getIndexedStatus(): ?int
    {
        return $this->hasData(self::INDEXED_STATUS) ? (int)$this->getData(self::INDEXED_STATUS) : null;
    }

    public function setIndexedStatus(int $status): CronJobInterface
    {
        return $this->setData(self::INDEXED_STATUS, $status);
    }

    public function getEntityCode(): ?string
    {
        return $this->hasData(self::ENTITY_CODE) ? $this->getData(self::ENTITY_CODE): null;
    }

    public function setEntityCode(string $entityCode): CronJobInterface
    {
        return $this->setData(self::ENTITY_CODE, $entityCode);
    }

    public function getProfileConfig(): ?ProfileConfigInterface
    {
        return $this->hasData(self::PROFILE_CONFIG) ? $this->getData(self::PROFILE_CONFIG) : null;
    }

    public function setProfileConfig(?ProfileConfigInterface $profileConfig): CronJobInterface
    {
        $this->setData(self::PROFILE_CONFIG, $profileConfig);

        return $this;
    }

    public function getSchedule(): ?\Amasty\CronSchedule\Api\Data\ScheduleInterface
    {
        return $this->hasData(self::SCHEDULE) ? $this->getData(self::SCHEDULE) : null;
    }

    public function setSchedule(\Amasty\CronSchedule\Api\Data\ScheduleInterface $schedule): CronJobInterface
    {
        $this->setData(self::SCHEDULE, $schedule);

        return $this;
    }
}
