<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Api\Data;

interface HistoryInterface
{
    /**
     * @return int|null
     */
    public function getHistoryId(): ?int;

    /**
     * @param int $id
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setHistoryId(int $id): HistoryInterface;

    /**
     * @return string|null
     */
    public function getEntityCode(): ?string;

    /**
     * @param string $type
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setEntityCode(string $type): HistoryInterface;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string $type
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setType(?string $type): HistoryInterface;

    /**
     * @return int|null
     */
    public function getJobId(): ?int;

    /**
     * @param int $jobId
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setJobId(?int $jobId): HistoryInterface;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setName(?string $name): HistoryInterface;

    /**
     * @return string|null
     */
    public function getExportedAt(): ?string;

    /**
     * @param string $exportedAt
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setExportedAt(string $exportedAt): HistoryInterface;

    /**
     * @return string|null
     */
    public function getFinishedAt(): ?string;

    /**
     * @param string $finishedAt
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setFinishedAt(string $finishedAt): HistoryInterface;

    /**
     * @return string|null
     */
    public function getIdentity(): ?string;

    /**
     * @param string $identity
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setIdentity(string $identity): HistoryInterface;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string $status
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setStatus(string $status): HistoryInterface;

    /**
     * @return string|null
     */
    public function getLog(): ?string;

    /**
     * @param string $log
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setLog(string $log): HistoryInterface;

    /**
     * @return bool
     */
    public function isDeletedFile(): bool;

    /**
     * @param bool $isDeleted
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function setIsDeletedFile(bool $isDeleted): HistoryInterface;
}
