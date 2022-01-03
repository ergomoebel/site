<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


namespace Amasty\OrderExport\Cron;

use Amasty\ExportPro\Model\History\Repository;
use Amasty\OrderExport\Model\ConfigProvider;
use Amasty\OrderExport\Model\ModuleType;

class CleanLogs
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        ConfigProvider $configProvider,
        Repository $repository
    ) {
        $this->configProvider = $configProvider;
        $this->repository = $repository;
    }

    public function execute()
    {
        if ($this->configProvider->getLogCleaning()) {
            $logPeriod = $this->configProvider->getLogPeriod();
            $this->repository->clearHistoryByDays(ModuleType::TYPE, $logPeriod);
        }

        if ($this->configProvider->getExportFiles()) {
            $filesPeriod = $this->configProvider->getFilesPeriod();
            $this->repository->clearFilesByDays(ModuleType::TYPE, $filesPeriod);
        }
    }
}
