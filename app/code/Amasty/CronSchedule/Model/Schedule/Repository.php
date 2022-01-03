<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


namespace Amasty\CronSchedule\Model\Schedule;

use Amasty\CronSchedule\Api\Data\ScheduleInterface;
use Amasty\CronSchedule\Api\Data\ScheduleInterfaceFactory;
use Amasty\CronSchedule\Api\ScheduleRepositoryInterface;
use Amasty\CronSchedule\Model\Schedule\ResourceModel\CollectionFactory;
use Amasty\CronSchedule\Model\Schedule\ResourceModel\Schedule as ScheduleResource;
use Amasty\CronSchedule\Utils\ObjectConverter;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Repository implements ScheduleRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ScheduleInterfaceFactory
     */
    private $scheduleFactory;

    /**
     * @var ScheduleResource
     */
    private $scheduleResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $schedules;

    /**
     * @var CollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var ObjectConverter
     */
    private $objectConverter;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        ScheduleInterfaceFactory $scheduleFactory,
        ScheduleResource $scheduleResource,
        ObjectConverter $objectConverter,
        CollectionFactory $scheduleCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->scheduleFactory = $scheduleFactory;
        $this->scheduleResource = $scheduleResource;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->objectConverter = $objectConverter;
    }

    public function save(ScheduleInterface $schedule)
    {
        try {
            if ($schedule->getScheduleId()) {
                $schedule = $this->getByJob($schedule->getJobType(), $schedule->getExternalId())
                    ->addData($schedule->getData());
            }

            $schedule->setSerializedExtensionAttributes(
                $this->objectConverter->serialize(
                    $schedule->getExtensionAttributes(),
                    \Amasty\CronSchedule\Api\Data\ScheduleExtensionInterface::class
                )
            );

            $this->scheduleResource->save($schedule);
            unset($this->schedules[$schedule->getScheduleId()]);
        } catch (\Exception $e) {
            if ($schedule->getScheduleId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save schedule with ID %1. Error: %2',
                        [$schedule->getScheduleId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new schedule. Error: %1', $e->getMessage()));
        }

        return $schedule;
    }

    public function getByJob($jobType, $externalId)
    {
        /** @var ScheduleInterface $scheduleModel */
        $scheduleModel = $this->scheduleCollectionFactory->create()
            ->addFieldToFilter(Schedule::JOB_TYPE, $jobType)
            ->addFieldToFilter(Schedule::EXTERNAL_ID, $externalId)
            ->getFirstItem();

        $this->unserializeExtensionAttributes($scheduleModel);

        return $scheduleModel;
    }

    private function unserializeExtensionAttributes(ScheduleInterface $scheduleModel)
    {
        if (!empty($scheduleModel->getSerializedExtensionAttributes())) {
            $scheduleModel->setExtensionAttributes(
                $this->objectConverter->unserialize(
                    $scheduleModel->getSerializedExtensionAttributes(),
                    \Amasty\CronSchedule\Api\Data\ScheduleExtensionInterface::class
                )
            );
        }
    }

    public function delete(ScheduleInterface $schedule)
    {
        try {
            $this->scheduleResource->delete($schedule);
            unset($this->schedules[$schedule->getScheduleId()]);
        } catch (\Exception $e) {
            if ($schedule->getScheduleId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove schedule with ID %1. Error: %2',
                        [$schedule->getScheduleId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove schedule. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteByJob($jobType, $externalId)
    {
        $scheduleModel = $this->getByJob($jobType, $externalId);
        $this->delete($scheduleModel);

        return true;
    }
}
