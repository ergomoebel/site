<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */


declare(strict_types=1);

namespace Amasty\OrderImport\Api\Data;

use Amasty\CronSchedule\Api\Data\ScheduleInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;

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
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setName(?string $name): ProfileInterface;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string|null $createdAt
     *
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setCreatedAt(?string $createdAt): ProfileInterface;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string|null $updatedAt
     *
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setUpdatedAt(?string $updatedAt): ProfileInterface;

    /**
     * @return string|null
     */
    public function getImportedAt(): ?string;

    /**
     * @param string|null $importedAt
     *
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setImportedAt(?string $importedAt): ProfileInterface;

    /**
     * @return string|null
     */
    public function getSerializedConfig(): ?string;

    /**
     * @param string|null $serializedConfig
     *
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setSerializedConfig(?string $serializedConfig): ProfileInterface;

    /**
     * @return ProfileConfigInterface|null
     */
    public function getConfig(): ?ProfileConfigInterface;

    /**
     * @param ProfileConfigInterface|null $profileConfig
     *
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setConfig(?ProfileConfigInterface $profileConfig): ProfileInterface;

    /**
     * @return string|null
     */
    public function getSourceType(): ?string;

    /**
     * @param string|null $fileFormat
     *
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setSourceType(?string $type): ProfileInterface;

    /**
     * @return string|null
     */
    public function getExecutionType(): ?string;

    /**
     * @param string|null $executionType
     *
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setExecutionType(?string $executionType): ProfileInterface;

    /**
     * @return ScheduleInterface|null
     */
    public function getSchedule(): ?ScheduleInterface;

    /**
     * @param ScheduleInterface $schedule
     *
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setSchedule(ScheduleInterface $schedule): ProfileInterface;

    /**
     * @return array|null
     */
    public function getOrderActions(): ?array;

    /**
     * @param array|null $actions
     *
     * @return \Amasty\OrderImport\Api\Data\ProfileInterface
     */
    public function setOrderActions(?array $actions): ProfileInterface;
}
