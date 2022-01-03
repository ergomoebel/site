<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Controller\Adminhtml\ExportJob;

use Amasty\ExportPro\Model\Job\ResourceModel\CollectionFactory;
use Amasty\ExportPro\Model\Job\Repository;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ExportPro::export_job_delete';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $jobCollectionFactory;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        Filter $filter,
        Action\Context $context,
        CollectionFactory $jobCollectionFactory,
        Repository $repository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->jobCollectionFactory = $jobCollectionFactory;
        $this->repository = $repository;
    }

    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();
        /** @var \Amasty\ExportPro\Model\Job\ResourceModel\Collection $collection */
        $collection = $this->filter->getCollection($this->jobCollectionFactory->create());

        if ($collection->getSize()) {
            foreach ($collection->getItems() as $job) {
                try {
                    $this->repository->delete($job);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }

        $this->messageManager->addSuccessMessage(__('Cron jobs was successfully removed.'));

        $this->_redirect('*/*');
    }
}
