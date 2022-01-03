<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


namespace Amasty\CronSchedule\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;

//TODO move to api
class DataProvider
{
    /**
     * @var array
     */
    private $dataProviders;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        DataProvider\Basic $basic,
        RequestInterface $request,
        array $dataProviders = null
    ) {
        $this->dataProviders = array_merge(['basic' => $basic], $dataProviders ?: []);
        $this->request = $request;
    }

    /**
     * @param $dataProviderType
     *
     * @return DataProvider\DataProviderInterface
     * @throws LocalizedException
     */
    public function getDataProvider($dataProviderType)
    {
        if (!isset($this->dataProviders[$dataProviderType])) {
            throw new LocalizedException(__('Cron Schedule dataprovider "%1" is not exist', $dataProviderType));
        }

        if (!is_subclass_of(
            $this->dataProviders[$dataProviderType],
            DataProvider\DataProviderInterface::class
        )) {
            throw new LocalizedException(
                __('Cron Schedule "%1" is not implement DataProviderInterface', $dataProviderType)
            );
        }

        return $this->dataProviders[$dataProviderType];
    }

    public function getData($jobType, $dataProviderType = 'basic', $jobId = null)
    {
        return $this->getDataProvider($dataProviderType)->getData($jobType, $jobId);
    }

    public function getMeta($jobType, array $arguments = [], $dataProviderType = 'basic')
    {
        return $this->getDataProvider($dataProviderType)->getMeta($jobType, $arguments);
    }

    public function prepareSchedule($jobType, $dataProviderType = 'basic', $jobId = null)
    {
        return $this->getDataProvider($dataProviderType)->prepareSchedule($this->request, $jobType, $jobId);
    }
}
