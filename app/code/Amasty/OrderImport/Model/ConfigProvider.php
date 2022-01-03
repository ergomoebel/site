<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */


namespace Amasty\OrderImport\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

/**
 * Scope config Provider model
 */
class ConfigProvider extends ConfigProviderAbstract
{
    protected $pathPrefix = 'amorderimport/';

    const BATCH_SIZE = 'general/batch_size';
    const LOG_CLEANING = 'general/log_cleaning';
    const LOG_PERIOD = 'general/log_period';
    const MULTI_PROCESS_ENABLED = 'multi_process/enabled';
    const MULTI_PROCESS_COUNT = 'multi_process/max_process_count';

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getBatchSize($storeId = null): int
    {
        return (int)$this->getValue(self::BATCH_SIZE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function getLogCleaning($storeId = null): bool
    {
        return $this->isSetFlag(self::LOG_CLEANING, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getLogPeriod($storeId = null): int
    {
        return (int)$this->getValue(self::LOG_PERIOD, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function useMultiProcess($storeId = null): bool
    {
        return $this->isSetFlag(self::MULTI_PROCESS_ENABLED, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getMaxProcessCount($storeId = null): int
    {
        return (int)$this->getValue(self::MULTI_PROCESS_COUNT, $storeId);
    }
}
