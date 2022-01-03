<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\Profile;

use Amasty\CronSchedule\Api\Data\ScheduleInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\OrderExport\Api\Data\ProfileInterface;
use Magento\Framework\Model\AbstractModel;

class Profile extends AbstractModel implements ProfileInterface
{
    const ID = 'id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const EXPORTED_AT = 'exported_at';
    const SERIALIZED_CONFIG = 'serialized_config';
    const CONFIG = 'config';
    const FILE_FORMAT = 'file_format';
    const EXECUTION_TYPE = 'execution_type';
    const ORDER_ACTIONS = 'order_actions';
    const SCHEDULE = 'schedule';
    const PROFILE_EVENT = 'profile_event';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Profile::class);
        $this->setIdFieldName(self::ID);
    }

    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    public function setName(?string $name): ProfileInterface
    {
        $this->setData(self::NAME, $name);

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(?string $createdAt): ProfileInterface
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }

    public function getExportedAt(): ?string
    {
        return $this->getData(self::EXPORTED_AT);
    }

    public function setExportedAt(?string $exportedAt): ProfileInterface
    {
        $this->setData(self::EXPORTED_AT, $exportedAt);

        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt(?string $updatedAt): ProfileInterface
    {
        $this->setData(self::UPDATED_AT, $updatedAt);

        return $this;
    }

    public function getSerializedConfig(): ?string
    {
        return $this->getData(self::SERIALIZED_CONFIG);
    }

    public function setSerializedConfig(?string $serializedConfig): ProfileInterface
    {
        $this->setData(self::SERIALIZED_CONFIG, $serializedConfig);

        return $this;
    }

    public function getConfig(): ?ProfileConfigInterface
    {
        return $this->getData(self::CONFIG);
    }

    public function setConfig(?ProfileConfigInterface $config): ProfileInterface
    {
        $this->setData(self::CONFIG, $config);

        return $this;
    }

    public function getFileFormat(): ?string
    {
        return $this->getData(self::FILE_FORMAT);
    }

    public function setFileFormat(?string $fileFormat): ProfileInterface
    {
        $this->setData(self::FILE_FORMAT, $fileFormat);

        return $this;
    }

    public function getExecutionType(): ?string
    {
        return $this->getData(self::EXECUTION_TYPE);
    }

    public function setExecutionType(?string $executionType): ProfileInterface
    {
        $this->setData(self::EXECUTION_TYPE, $executionType);

        return $this;
    }

    public function getOrderActions(): ?array
    {
        return $this->getData(self::ORDER_ACTIONS);
    }

    public function setOrderActions(?array $actions): ProfileInterface
    {
        $this->setData(self::ORDER_ACTIONS, $actions);

        return $this;
    }

    public function getSchedule(): ?ScheduleInterface
    {
        return $this->getData(self::SCHEDULE);
    }

    public function setSchedule(?ScheduleInterface $schedule): ProfileInterface
    {
        $this->setData(self::SCHEDULE, $schedule);

        return $this;
    }

    public function getProfileEvents(): ?array
    {
        return $this->getData(self::PROFILE_EVENT);
    }

    public function setProfileEvents(array $profileEvents): ProfileInterface
    {
        $this->setData(self::PROFILE_EVENT, $profileEvents);

        return $this;
    }
}
