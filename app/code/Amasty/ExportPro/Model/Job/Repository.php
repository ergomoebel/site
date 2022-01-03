<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Model\Job;

use Amasty\CronSchedule\Api\ScheduleRepositoryInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportPro\Api\CronJobRepositoryInterface;
use Amasty\ExportPro\Api\Data\CronJobInterface;
use Amasty\ExportPro\Api\Data\CronJobInterfaceFactory;
use Amasty\ExportPro\Model\Job\ResourceModel\CollectionFactory;
use Amasty\ExportPro\Model\Job\ResourceModel\Job as JobResource;
use Amasty\ImportExportCore\Utils\Serializer;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

class Repository implements CronJobRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CronJobInterfaceFactory
     */
    private $jobFactory;

    /**
     * @var JobResource
     */
    private $jobResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $jobs;

    /**
     * @var CollectionFactory
     */
    private $jobCollectionFactory;

    /**
     * @var ScheduleRepositoryInterface
     */
    private $scheduleRepository;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        CronJobInterfaceFactory $jobFactory,
        JobResource $jobResource,
        CollectionFactory $jobCollectionFactory,
        Serializer $serializer,
        ScheduleRepositoryInterface $scheduleRepository
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->jobFactory = $jobFactory;
        $this->jobResource = $jobResource;
        $this->jobCollectionFactory = $jobCollectionFactory;
        $this->scheduleRepository = $scheduleRepository;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function save(CronJobInterface $job)
    {
        try {
            if ($job->getJobId()) {
                $job = $this->getById($job->getJobId())->addData($job->getData());
            }
            if ($job->getProfileConfig()) {
                $job->getProfileConfig()->initialize();
                $job->setConfig(
                    $this->serializer->serialize($job->getProfileConfig(), ProfileConfigInterface::class)
                );
            } else {
                $job->setConfig(null);
            }

            $this->jobResource->save($job);

            if ($scheduleModel = $job->getSchedule()) {
                $scheduleModel->setExternalId($job->getJobId());
                $this->scheduleRepository->save($scheduleModel);
            }

            unset($this->jobs[$job->getJobId()]);
        } catch (\Exception $e) {
            if ($job->getJobId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save job with ID %1. Error: %2',
                        [$job->getJobId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new job. Error: %1', $e->getMessage()));
        }

        return $job;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->jobs[$id])) {
            /** @var \Amasty\ExportPro\Model\Job\Job $job */
            $job = $this->jobFactory->create();
            $this->jobResource->load($job, $id);
            if (!$job->getJobId()) {
                throw new NoSuchEntityException(__('Job with specified ID "%1" not found.', $id));
            }
            $job->setProfileConfig(
                $this->serializer->unserialize($job->getConfig(), ProfileConfigInterface::class)
            );
            $this->jobs[$id] = $job;
        }

        return $this->jobs[$id];
    }

    /**
     * @inheritdoc
     */
    public function delete(CronJobInterface $job)
    {
        try {
            $this->jobResource->delete($job);
            $this->scheduleRepository->deleteByJob(Job::JOB_EXPORT_TYPE, $job->getJobId());
            unset($this->jobs[$job->getJobId()]);
        } catch (\Exception $e) {
            if ($job->getJobId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove job with ID %1. Error: %2',
                        [$job->getJobId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove job. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $jobModel = $this->getById($id);
        $this->delete($jobModel);

        return true;
    }

    /**
     * @return \Amasty\ExportPro\Api\Data\CronJobInterface
     */
    public function getEmptyJobModel()
    {
        return $this->jobFactory->create();
    }
}
