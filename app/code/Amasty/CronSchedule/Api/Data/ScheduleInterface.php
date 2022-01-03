<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


declare(strict_types=1);

namespace Amasty\CronSchedule\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface ScheduleInterface extends ExtensibleDataInterface
{
    /**
     * @return int|null
     */
    public function getScheduleId(): ?int;

    /**
     * @param int|null $id
     *
     * @return \Amasty\CronSchedule\Api\Data\ScheduleInterface
     */
    public function setScheduleId(?int $id): ScheduleInterface;

    /**
     * @return string|null
     */
    public function getExpression(): ?string;

    /**
     * @param string|null $expression
     *
     * @return \Amasty\CronSchedule\Api\Data\ScheduleInterface
     */
    public function setExpression(?string $expression): ScheduleInterface;

    /**
     * @return string|null
     */
    public function getJobType(): ?string;

    /**
     * @param string|null $jobType
     *
     * @return \Amasty\CronSchedule\Api\Data\ScheduleInterface
     */
    public function setJobType(?string $jobType): ScheduleInterface;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param bool|null $enabled
     *
     * @return \Amasty\CronSchedule\Api\Data\ScheduleInterface
     */
    public function setIsEnabled(?bool $enabled): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getExternalId(): ?int;

    /**
     * @param int $externalId
     *
     * @return \Amasty\CronSchedule\Api\Data\ScheduleInterface
     */
    public function setExternalId(?int $externalId): ScheduleInterface;

    /**
     * @return string|null
     */
    public function getSerializedExtensionAttributes(): ?string;

    /**
     * @param string|null $serializedExtensionAttributes
     *
     * @return \Amasty\CronSchedule\Api\Data\ScheduleInterface
     */
    public function setSerializedExtensionAttributes(?string $serializedExtensionAttributes): ScheduleInterface;

    /**
     * @return \Amasty\CronSchedule\Api\Data\ScheduleExtensionInterface
     */
    public function getExtensionAttributes(): \Amasty\CronSchedule\Api\Data\ScheduleExtensionInterface;

    /**
     * @param \Amasty\CronSchedule\Api\Data\ScheduleExtensionInterface $extensionAttributes
     *
     * @return \Amasty\CronSchedule\Api\Data\ScheduleInterface
     */
    public function setExtensionAttributes(
        \Amasty\CronSchedule\Api\Data\ScheduleExtensionInterface $extensionAttributes
    ): ScheduleInterface;
}
