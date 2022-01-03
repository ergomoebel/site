<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Model\History;

use Amasty\ImportPro\Api\Data\HistoryInterface;
use Amasty\ImportPro\Api\Data\HistoryInterfaceFactory;
use Amasty\ImportPro\Api\HistoryRepositoryInterface;
use Amasty\ImportPro\Model\History\ResourceModel\CollectionFactory;
use Amasty\ImportPro\Model\History\ResourceModel\History as HistoryResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

class Repository implements HistoryRepositoryInterface
{
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
    private $date;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        HistoryInterfaceFactory $historyFactory,
        HistoryResource $historyResource,
        CollectionFactory $historyCollectionFactory,
        DateTime $date
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->historyFactory = $historyFactory;
        $this->historyResource = $historyResource;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->date = $date;
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
            /** @var \Amasty\ImportExportHistory\Model\History\Import\History $history */
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
     * @return \Amasty\ImportPro\Api\Data\HistoryInterface
     */
    public function getEmptyHistoryModel()
    {
        return $this->historyFactory->create();
    }

    public function clearHistory()
    {
        try {
            $this->historyResource->clearTable();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getByIdentity($identity)
    {
        return $this->historyCollectionFactory->create()
            ->addFieldToFilter(History::IDENTITY, $identity)
            ->getFirstItem();
    }

    public function clearHistoryByDays(string $jobType, int $days)
    {
        $dateToDelete = $this->date->gmtDate('Y-m-d H:i:s', '-' . $days . 'days');
        $this->historyResource->clearHistoryByDate($jobType, $dateToDelete);
    }
}
