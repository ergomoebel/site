<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


declare(strict_types=1);

namespace Amasty\CronSchedule\Model\Schedule;

use Amasty\CronSchedule\Api\Data\ScheduleExtensionInterfaceFactory;
use Amasty\CronSchedule\Api\Data\ScheduleInterface;
use Magento\Framework\Model\AbstractModel;

class Schedule extends AbstractModel implements ScheduleInterface
{
    const SCHEDULE_ID = 'schedule_id';
    const JOB_TYPE = 'job_type';
    const ENABLED = 'enabled';
    const EXTERNAL_ID = 'external_id';
    const EXPRESSION = 'expression';
    const SERIALIZED_EXTENSION_ATTRIBUTES = 'serialized_extension_attributes';
    const EVENT_NAME = 'amcron_run_';

    /**
     * @var ScheduleExtensionInterfaceFactory
     */
    private $extensionAttributesFactory;

    public function __construct(
        ScheduleExtensionInterfaceFactory $extensionAttributesFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Schedule::class);
        $this->setIdFieldName(self::SCHEDULE_ID);
    }

    public function getScheduleId(): int
    {
        return (int)$this->getData(self::SCHEDULE_ID);
    }

    public function setScheduleId(?int $id): ScheduleInterface
    {
        $this->setData(self::SCHEDULE_ID, $id);

        return $this;
    }

    public function getExpression(): ?string
    {
        return $this->getData(self::EXPRESSION);
    }

    public function setExpression(?string $expression): ScheduleInterface
    {
        $this->setData(self::EXPRESSION, $expression);

        return $this;
    }

    public function getJobType(): ?string
    {
        return $this->getData(self::JOB_TYPE);
    }

    public function setJobType(?string $jobType): ScheduleInterface
    {
        $this->setData(self::JOB_TYPE, $jobType);

        return $this;
    }

    public function isEnabled(): bool
    {
        return (bool)$this->getData(self::ENABLED);
    }

    public function setIsEnabled(?bool $enabled): ScheduleInterface
    {
        $this->setData(self::ENABLED, $enabled);

        return $this;
    }

    public function getExternalId(): int
    {
        return (int)$this->getData(self::EXTERNAL_ID);
    }

    public function setExternalId(?int $externalId): ScheduleInterface
    {
        $this->setData(self::EXTERNAL_ID, $externalId);

        return $this;
    }

    public function getSerializedExtensionAttributes(): ?string
    {
        return $this->_getData(self::SERIALIZED_EXTENSION_ATTRIBUTES);
    }

    public function setSerializedExtensionAttributes(?string $serializedExtensionAttributes): ScheduleInterface
    {
        $this->setData(self::SERIALIZED_EXTENSION_ATTRIBUTES, $serializedExtensionAttributes);

        return $this;
    }

    public function getExtensionAttributes(): \Amasty\CronSchedule\Api\Data\ScheduleExtensionInterface
    {
        if ($this->_getData(self::EXTENSION_ATTRIBUTES_KEY) === null) {
            $this->setExtensionAttributes($this->extensionAttributesFactory->create());
        }

        return $this->_getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    public function setExtensionAttributes(
        \Amasty\CronSchedule\Api\Data\ScheduleExtensionInterface $extensionAttributes
    ): ScheduleInterface {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);

        return $this;
    }
}
