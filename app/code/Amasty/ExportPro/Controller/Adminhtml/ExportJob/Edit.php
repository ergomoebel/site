<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Controller\Adminhtml\ExportJob;

use Amasty\ExportPro\Api\CronJobRepositoryInterface;
use Amasty\ExportPro\Model\Job\Job;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ExportPro::export_job_edit';

    /**
     * @var CronJobRepositoryInterface
     */
    private $repository;

    public function __construct(
        CronJobRepositoryInterface $repository,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ExportPro::exportcron');

        if ($jobId = (int)$this->getRequest()->getParam(Job::JOB_ID)) {
            try {
                $this->repository->getById($jobId);
                $resultPage->getConfig()->getTitle()->prepend(__('Edit Job'));
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This job no longer exists.'));

                return $this->_redirect('*/*/index');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Export Job'));
        }

        return $resultPage;
    }
}
