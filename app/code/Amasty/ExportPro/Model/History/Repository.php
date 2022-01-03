<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\History;

use Amasty\ExportCore\Export\Utils\CleanUpByProcessIdentity;
use Amasty\ExportPro\Api\Data\HistoryInterface;
use Amasty\ExportPro\Api\Data\HistoryInterfaceFactory;
use Amasty\ExportPro\Api\HistoryRepositoryInterface;
use Amasty\ExportPro\Model\History\ResourceModel\CollectionFactory;
use Amasty\ExportPro\Model\History\ResourceModel\History as HistoryResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

class Repository implements HistoryRepositoryInterface
{
    /**
     * @var CleanUpByProcessIdentity
     */
    private $cleanUpByProcessIdentity;

    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var HistoryInterfaceFactory
     */
    private $historyFactory;

    /**
     * @var HistoryResource
     */
    private $historyResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $history;

    /**
     * @var CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var DateTime\DateTime
     */
    private $date;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        HistoryInterfaceFactory $historyFactory,
        HistoryResource $historyResource,
        DateTime $dateTime,
        DateTime\DateTime $date,
        CleanUpByProcessIdentity $cleanUpByProcessIdentity,
        CollectionFactory $historyCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->historyFactory = $historyFactory;
        $this->historyResource = $historyResource;
        $this->dateTime = $dateTime;
        $this->date = $date;
        $this->cleanUpByProcessIdentity = $cleanUpByProcessIdentity;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    public function save(HistoryInterface $history)
    {
        try {
            if ($history->getHistoryId()) {
                $history = $this->getById($history->getHistoryId())->addData($history->getData());
            }
            $this->historyResource->save($history);

            unset($this->history[$history->getHistoryId()]);
        } catch (\Exception $e) {
            if ($history->getHistoryId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save history with ID %1. Error: %2',
                        [$history->getHistoryId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new history. Error: %1', $e->getMessage()));
        }

        return $history;
    }

    public function getById($id)
    {
        if (!isset($this->history[$id])) {
            /** @var \Amasty\ExportPro\Model\History\History $history */
            $history = $this->historyFactory->create();
            $this->historyResource->load($history, $id);
            if (!$history->getHistoryId()) {
                throw new NoSuchEntityException(__('History with specified ID "%1" not found.', $id));
            }
            $this->history[$id] = $history;
        }

        return $this->history[$id];
    }

    public function delete(HistoryInterface $history)
    {
        try {
            if (!$history->isDeletedFile()) {
                $this->cleanUpByProcessIdentity->execute($history->getIdentity());
            }

            $this->historyResource->delete($history);

            unset($this->history[$history->getHistoryId()]);
        } catch (\Exception $e) {
            if ($history->getHistoryId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove history with ID %1. Error: %2',
                        [$history->getHistoryId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove history. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteById($id)
    {
        $historyModel = $this->getById($id);
        $this->delete($historyModel);

        return true;
    }

    /**
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function getEmptyHistoryModel()
    {
        return $this->historyFactory->create();
    }

    public function clearHistory(string $jobType)
    {
        try {
            $collection = $this->getByJobType($jobType);
            foreach ($collection->getItems() as $deleteItem) {
                if (!$deleteItem->isDeletedFile()) {
                    $this->cleanUpByProcessIdentity->execute($deleteItem->getIdentity());
                }
            }

            $this->historyResource->clearHistory($jobType);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $jobType
     * @param int $days
     *
     * @return mixed|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearHistoryByDays(string $jobType, int $days)
    {
        $collection = $this->getByDays($jobType, $days);
        $toDelete = [];

        foreach ($collection->getItems() as $deleteItem) {
            $toDelete[] = $deleteItem->getId();
            if (!$deleteItem->isDeletedFile()) {
                $this->cleanUpByProcessIdentity->execute($deleteItem->getIdentity());
            }
        }

        if (!empty($toDelete)) {
            $this->historyResource->deleteByHistoryIds($toDelete);
        }
    }

    /**
     * @param string $jobType
     * @param int $days
     *
     * @throws CouldNotSaveException
     */
    public function clearFilesByDays(string $jobType, int $days)
    {
        $collection = $this->getByDays($jobType, $days);
        $collection->addFieldToFilter(History::IS_DELETED_FILE, 0);

        foreach ($collection->getItems() as $deleteItem) {
            $this->cleanUpByProcessIdentity->execute($deleteItem->getIdentity());
            $deleteItem->setIsDeletedFile(true);
            $this->save($deleteItem);
        }
    }

    /**
     * @param string $jobType
     * @param int $id
     * @throws CouldNotDeleteException
     */
    public function clearByJobTypeAndId(string $jobType, int $id)
    {
        $collection = $this->getByJobType($jobType)
            ->addFieldToFilter(History::JOB_ID, $id);

        foreach ($collection as $history) {
            $this->delete($history);
        }
    }

    /**
     * @return \Amasty\ExportPro\Model\History\ResourceModel\Collection
     */
    public function getByDays(string $jobType, int $days)
    {
        $dateToDelete = $this->date->gmtDate('Y-m-d H:i:s', '-' . $days . 'days');
        $collection = $this->historyCollectionFactory->create()
            ->addFieldToFilter(History::TYPE, $jobType)
            ->addFieldToFilter(History::FINISHED_AT, ['lt' => $dateToDelete]);

        return $collection;
    }

    /**
     * @return \Amasty\ExportPro\Model\History\ResourceModel\Collection
     */
    public function getByJobType(string $jobType)
    {
        $collection = $this->historyCollectionFactory->create()
            ->addFieldToFilter(History::TYPE, $jobType);

        return $collection;
    }

    public function getByIdentity($identity)
    {
        return $this->historyCollectionFactory->create()
            ->addFieldToFilter(History::IDENTITY, $identity)
            ->getFirstItem();
    }
}
