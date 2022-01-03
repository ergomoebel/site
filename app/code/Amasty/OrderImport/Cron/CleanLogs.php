<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Cron;

use Amasty\ImportPro\Model\History\Repository;
use Amasty\OrderImport\Model\ConfigProvider;
use Amasty\OrderImport\Model\ModuleType;

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

    public function execute(): void
    {
        if ($this->configProvider->getLogCleaning()) {
            $logPeriod = $this->configProvider->getLogPeriod();
            $this->repository->clearHistoryByDays(ModuleType::TYPE, $logPeriod);
        }
    }
}
