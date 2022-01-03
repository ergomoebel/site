<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Controller\Adminhtml\Profile;

use Amasty\OrderExport\Api\ProfileRepositoryInterface;
use Amasty\OrderExport\Model\Profile\ResourceModel\Collection;
use Amasty\OrderExport\Model\Profile\ResourceModel\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderExport::order_export_profiles';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProfileRepositoryInterface
     */
    private $repository;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Action\Context $context,
        ProfileRepositoryInterface $repository,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->repository = $repository;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();
        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        if ($collection->getSize()) {
            foreach ($collection->getItems() as $job) {
                try {
                    $this->repository->delete($job);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    $this->logger->critical($e);
                }
            }
        }

        $this->messageManager->addSuccessMessage(__('Profile(s) was successfully removed.'));

        $this->_redirect('*/*');
    }
}
