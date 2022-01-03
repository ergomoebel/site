<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Api\Data;

interface HistoryInterface
{
    /**
     * @return int
     */
    public function getHistoryId(): ?int;

    /**
     * @param int $id
     *
     * @return void
     */
    public function setHistoryId(int $id): void;

    /**
     * @return string|null
     */
    public function getEntityCode(): ?string;

    /**
     * @param string $type
     *
     * @return void
     */
    public function setEntityCode(string $type): void;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string|null $type
     *
     * @return void
     */
    public function setType(?string $type): void;

    /**
     * @return int|null
     */
    public function getJobId(): ?int;

    /**
     * @param int $jobId
     *
     * @return void
     */
    public function setJobId(?int $jobId): void;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getStartedAt(): ?string;

    /**
     * @param string $startedAt
     *
     * @return void
     */
    public function setStartedAt(string $startedAt): void;

    /**
     * @return null|string
     */
    public function getFinishedAt(): ?string;

    /**
     * @param string $finishedAt
     *
     * @return void
     */
    public function setFinishedAt(string $finishedAt): void;

    /**
     * @return null|string
     */
    public function getIdentity(): ?string;

    /**
     * @param string $identity
     *
     * @return void
     */
    public function setIdentity(string $identity): void;

    /**
     * @return null|string
     */
    public function getStatus(): ?string;

    /**
     * @param string $status
     *
     * @return void
     */
    public function setStatus(string $status): void;

    /**
     * @return null|string
     */
    public function getLog(): ?string;

    /**
     * @param string $log
     *
     * @return void
     */
    public function setLog(string $log): void;
}
