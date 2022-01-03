<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Api\Data;

use Amasty\CronSchedule\Api\Data\ScheduleInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;

interface ProfileInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int|null $id
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setName(?string $name): ProfileInterface;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string|null $createdAt
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setCreatedAt(?string $createdAt): ProfileInterface;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string|null $updatedAt
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setUpdatedAt(?string $updatedAt): ProfileInterface;

    /**
     * @return string|null
     */
    public function getExportedAt(): ?string;

    /**
     * @param string|null $exportedAt
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setExportedAt(?string $exportedAt): ProfileInterface;

    /**
     * @return string|null
     */
    public function getSerializedConfig(): ?string;

    /**
     * @param string|null $serializedConfig
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setSerializedConfig(?string $serializedConfig): ProfileInterface;

    /**
     * @return ProfileConfigInterface|null
     */
    public function getConfig(): ?ProfileConfigInterface;

    /**
     * @param ProfileConfigInterface|null $profileConfig
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setConfig(?ProfileConfigInterface $profileConfig): ProfileInterface;

    /**
     * @return string|null
     */
    public function getFileFormat(): ?string;

    /**
     * @param string|null $fileFormat
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setFileFormat(?string $fileFormat): ProfileInterface;

    /**
     * @return string|null
     */
    public function getExecutionType(): ?string;

    /**
     * @param string|null $executionType
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setExecutionType(?string $executionType): ProfileInterface;

    /**
     * @return ScheduleInterface|null
     */
    public function getSchedule(): ?ScheduleInterface;

    /**
     * @param ScheduleInterface $schedule
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setSchedule(ScheduleInterface $schedule): ProfileInterface;

    /**
     * @return string|null
     */
    public function getOrderActions(): ?array;

    /**
     * @param string|null $actions
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setOrderActions(?array $actions): ProfileInterface;

    /**
     * @return array|null
     */
    public function getProfileEvents(): ?array;

    /**
     * @param array $profileEvents
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileInterface
     */
    public function setProfileEvents(array $profileEvents): ProfileInterface;
}
