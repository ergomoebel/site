<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */

declare(strict_types=1);

namespace Amasty\CustomerImport\Cron;

use Amasty\CustomerImport\Model\ConfigProvider;
use Amasty\CustomerImport\Model\ModuleType;
use Amasty\ImportPro\Model\History\Repository;

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
