<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Controller\Adminhtml\History;

use Amasty\ExportPro\Model\History\Repository;
use Amasty\ExportPro\Model\History\ResourceModel\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ExportPro::export_history_delete';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $historyCollectionFactory,
        Repository $repository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();
        /** @var \Amasty\ExportPro\Model\History\ResourceModel\Collection $collection */
        $collection = $this->filter->getCollection($this->historyCollectionFactory->create());
        $deleted = 0;
        $failed = 0;

        foreach ($collection->getItems() as $history) {
            try {
                $this->repository->delete($history);
                $deleted++;
            } catch (CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $failed++;
            } catch (\Exception $e) {
                $this->logger->error(
                    __('Error occurred while deleting History Record with ID %1. Error: %2'),
                    [$history->getId(), $e->getMessage()]
                );
            }
        }

        if ($deleted !== 0) {
            $this->messageManager->addSuccessMessage(
                __('%1 History Record(s) has been successfully deleted', $deleted)
            );
        }

        if ($failed !== 0) {
            $this->messageManager->addErrorMessage(
                __('%1 History Record(s) has been failed to delete', $failed)
            );
        }

        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
