<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


namespace Amasty\CronSchedule\Api;

use Amasty\CronSchedule\Api\Data\ScheduleInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NotFoundException as NotFoundExceptionAlias;

/**
 * @api
 */
interface ScheduleRepositoryInterface
{
    /**
     * @param ScheduleInterface $schedule
     *
     * @return ScheduleInterface
     */
    public function save(ScheduleInterface $schedule);

    /**
     * @param string $jobType
     * @param int $externalId
     *
     * @return ScheduleInterface
     * @throws NotFoundExceptionAlias
     */
    public function getByJob($jobType, $externalId);

    /**
     * @param ScheduleInterface $schedule
     *
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(ScheduleInterface $schedule);

    /**
     * Delete by external Id
     *
     * @param string $jobType
     * @param int $externalId
     *
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function deleteByJob($jobType, $externalId);
}
