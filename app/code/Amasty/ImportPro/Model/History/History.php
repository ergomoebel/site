<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Model\History;

use Amasty\ImportPro\Api\Data\HistoryInterface;
use Magento\Framework\Model\AbstractModel;

class History extends AbstractModel implements HistoryInterface
{
    const HISTORY_ID = 'history_id';
    const TYPE = 'type';
    const JOB_ID = 'job_id';
    const NAME = 'name';
    const ENTITY_CODE = 'entity_code';
    const STARTED_AT = 'started_at';
    const FINISHED_AT = 'finished_at';
    const IDENTITY = 'identity';
    const LOG = 'log';
    const STATUS = 'status';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\History::class);
        $this->setIdFieldName(self::HISTORY_ID);
    }

    public function getHistoryId(): ?int
    {
        return (int)$this->getData(self::HISTORY_ID);
    }

    public function setHistoryId(int $id): void
    {
        $this->setData(self::HISTORY_ID, (int)$id);
    }

    public function getEntityCode(): ?string
    {
        return $this->getData(self::ENTITY_CODE);
    }

    public function setEntityCode(string $entityCode): void
    {
        $this->setData(self::ENTITY_CODE, $entityCode);
    }

    public function getType(): ?string
    {
        return $this->getData(self::TYPE);
    }

    public function setType(?string $type): void
    {
        $this->setData(self::TYPE, $type);
    }

    public function getJobId(): ?int
    {
        return $this->getData(self::JOB_ID);
    }

    public function setJobId(?int $jobId): void
    {
        $this->setData(self::JOB_ID, $jobId);
    }

    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    public function setName(?string $name): void
    {
        $this->setData(self::NAME, $name);
    }

    public function getStartedAt(): ?string
    {
        return $this->getData(self::STARTED_AT);
    }

    public function setStartedAt(string $startedAt): void
    {
        $this->setData(self::STARTED_AT, $startedAt);
    }

    public function getFinishedAt(): ?string
    {
        return $this->getData(self::FINISHED_AT);
    }

    public function setFinishedAt(string $finishedAt): void
    {
        $this->setData(self::FINISHED_AT, $finishedAt);
    }

    public function getIdentity(): ?string
    {
        return $this->getData(self::IDENTITY);
    }

    public function setIdentity(string $identity): void
    {
        $this->setData(self::IDENTITY, $identity);
    }

    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus(string $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    public function getLog(): ?string
    {
        return $this->getData(self::LOG);
    }

    public function setLog(string $log): void
    {
        $this->setData(self::LOG, $log);
    }
}
