<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


namespace Amasty\ProductExport\Cron;

use Amasty\ExportPro\Model\History\Repository;
use Amasty\ProductExport\Model\ConfigProvider;
use Amasty\ProductExport\Model\ModuleType;

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
