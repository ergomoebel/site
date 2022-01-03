<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\History;

use Amasty\ExportPro\Api\Data\HistoryInterface;
use Magento\Framework\Model\AbstractModel;

class History extends AbstractModel implements HistoryInterface
{
    const HISTORY_ID = 'history_id';
    const TYPE = 'type';
    const JOB_ID = 'job_id';
    const NAME = 'name';
    const ENTITY_CODE = 'entity_code';
    const EXPORTED_AT = 'exported_at';
    const FINISHED_AT = 'finished_at';
    const IDENTITY = 'identity';
    const LOG = 'log';
    const STATUS = 'status';
    const IS_DELETED_FILE = 'is_deleted_file';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\History::class);
        $this->setIdFieldName(self::HISTORY_ID);
    }

    public function getHistoryId(): int
    {
        return (int)$this->_getData(self::HISTORY_ID);
    }

    public function setHistoryId(int $id): HistoryInterface
    {
        $this->setData(self::HISTORY_ID, (int)$id);

        return $this;
    }

    public function getEntityCode(): ?string
    {
        return $this->_getData(self::ENTITY_CODE);
    }

    public function setEntityCode(string $entityCode): HistoryInterface
    {
        $this->setData(self::ENTITY_CODE, $entityCode);

        return $this;
    }

    public function getType(): ?string
    {
        return $this->_getData(self::TYPE);
    }

    public function setType(?string $type): HistoryInterface
    {
        $this->setData(self::TYPE, $type);

        return $this;
    }

    public function getJobId(): ?int
    {
        return $this->_getData(self::JOB_ID);
    }

    public function setJobId(?int $jobId): HistoryInterface
    {
        $this->setData(self::JOB_ID, $jobId);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->_getData(self::NAME);
    }

    public function setName(?string $name): HistoryInterface
    {
        $this->setData(self::NAME, $name);

        return $this;
    }

    public function getExportedAt(): string
    {
        return $this->_getData(self::EXPORTED_AT);
    }

    public function setExportedAt(string $exportedAt): HistoryInterface
    {
        $this->setData(self::EXPORTED_AT, $exportedAt);

        return $this;
    }

    public function getFinishedAt(): string
    {
        return $this->_getData(self::FINISHED_AT);
    }

    public function setFinishedAt(string $finishedAt): HistoryInterface
    {
        $this->setData(self::FINISHED_AT, $finishedAt);

        return $this;
    }

    public function getIdentity(): string
    {
        return $this->_getData(self::IDENTITY);
    }

    public function setIdentity(string $identity): HistoryInterface
    {
        $this->setData(self::IDENTITY, $identity);

        return $this;
    }

    public function getStatus(): string
    {
        return $this->_getData(self::STATUS);
    }

    public function setStatus(string $status): HistoryInterface
    {
        $this->setData(self::STATUS, $status);

        return $this;
    }

    public function getLog(): string
    {
        return $this->_getData(self::LOG);
    }

    public function setLog(string $log): HistoryInterface
    {
        $this->setData(self::LOG, $log);

        return $this;
    }

    public function isDeletedFile(): bool
    {
        return (bool)$this->_getData(self::IS_DELETED_FILE);
    }

    public function setIsDeletedFile(bool $isDeleted): HistoryInterface
    {
        $this->setData(self::IS_DELETED_FILE, $isDeleted);

        return $this;
    }
}
