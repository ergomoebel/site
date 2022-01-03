<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Model\Job;

use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportPro\Api\Data\CronJobInterface;
use Magento\Framework\Model\AbstractModel;

class Job extends AbstractModel implements CronJobInterface
{
    const JOB_ID = 'job_id';
    const TITLE = 'title';
    const STATUS = 'status';
    const GROUP = 'group';
    const CONFIG = 'config';
    const PROFILE_CONFIG = 'profile_config';
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const ENTITY_CODE = 'entity_code';
    const SCHEDULE = 'schedule';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Job::class);
        $this->setIdFieldName(self::JOB_ID);
    }

    /**
     * @inheritdoc
     */
    public function getJobId()
    {
        return (int)$this->getData(self::JOB_ID);
    }

    public function setJobId(int $id): CronJobInterface
    {
        return $this->setData(self::JOB_ID, (int)$id);
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return $this->getData(self::CONFIG);
    }

    /**
     * @inheritdoc
     */
    public function setConfig(?string $config): CronJobInterface
    {
        return $this->setData(self::CONFIG, $config);
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
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): CronJobInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus(int $status): CronJobInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getEntityCode()
    {
        return $this->getData(self::ENTITY_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setEntityCode(string $entityCode): CronJobInterface
    {
        return $this->setData(self::ENTITY_CODE, $entityCode);
    }

    public function getSchedule(): ?\Amasty\CronSchedule\Api\Data\ScheduleInterface
    {
        return $this->getData(self::SCHEDULE);
    }

    public function setSchedule(\Amasty\CronSchedule\Api\Data\ScheduleInterface $schedule): void
    {
        $this->setData(self::SCHEDULE, $schedule);
    }
}
