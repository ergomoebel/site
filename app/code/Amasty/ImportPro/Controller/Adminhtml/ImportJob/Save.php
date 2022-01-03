<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Controller\Adminhtml\ImportJob;

use Amasty\CronSchedule\Model\DataProvider;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Import\FormProvider;
use Amasty\ImportPro\Api\CronJobRepositoryInterface;
use Amasty\ImportCore\Import\Config\ProfileConfigFactory;
use Amasty\ImportPro\Model\Job\DataProvider\CompositeFormType;
use Amasty\ImportPro\Model\Job\Job;
use Amasty\ImportPro\Model\Job\ScheduleConfig;
use Amasty\ImportPro\Model\ModuleType;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ImportPro::import_job_edit';

    /**
     * @var CronJobRepositoryInterface
     */
    private $jobRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProfileConfigFactory
     */
    private $profileConfigFactory;

    /**
     * @var DataProvider
     */
    private $scheduleDataProvider;

    /**
     * @var FormProvider
     */
    private $formProvider;

    public function __construct(
        CronJobRepositoryInterface $jobRepository,
        Action\Context $context,
        LoggerInterface $logger,
        DataProvider $scheduleDataProvider,
        FormProvider $formProvider,
        ProfileConfigFactory $profileConfigFactory
    ) {
        parent::__construct($context);
        $this->jobRepository = $jobRepository;
        $this->logger = $logger;
        $this->profileConfigFactory = $profileConfigFactory;
        $this->scheduleDataProvider = $scheduleDataProvider;
        $this->formProvider = $formProvider;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $data = $this->getRequest()->getParam('encodedData');
        if (!empty($data)) {
            $params = $this->getRequest()->getParams();
            unset($params['encodedData']);
            $postData = \json_decode($data, true);
            $this->getRequest()->setParams(array_merge_recursive($params, $postData));
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultData = [];
        try {
            if ($data = $this->getRequest()->getParams()) {
                /** @var ProfileConfigInterface $profileConfig */
                $profileConfig = $this->profileConfigFactory->create();
                $profileConfig->setStrategy('validate_and_import');
                $profileConfig->setEntityCode($this->getRequest()->getParam('entity_code'));
                $this->formProvider->get(CompositeFormType::TYPE)
                    ->prepareConfig($profileConfig, $this->getRequest());

                if ($jobId = (int)$this->getRequest()->getParam(Job::JOB_ID)) {
                    $model = $this->jobRepository->getById($jobId);
                } else {
                    $model = $this->jobRepository->getEmptyJobModel();
                }

                $model->addData($data);
                $model->setSchedule(
                    $this->scheduleDataProvider->prepareSchedule(
                        ModuleType::TYPE,
                        ScheduleConfig::DATAPROVIDER_TYPE,
                        $model->getJobId()
                    )
                );
                $model->setStatus((int)$model->getSchedule()->isEnabled());
                $model->setProfileConfig($profileConfig);
                $this->jobRepository->save($model);
                $successMessage = __('You saved the job.');
                if ($this->getRequest()->getParam('back')) {
                    if (!$jobId) {
                        $this->messageManager->addSuccessMessage($successMessage);
                        $resultData['redirect'] = $this->_url->getUrl('*/*/edit', [Job::JOB_ID => $model->getJobId()]);
                    } else {
                        $resultData['messages']['success'] = $successMessage;
                    }
                } else {
                    $this->messageManager->addSuccessMessage($successMessage);
                    $resultData['redirect'] = $this->_url->getUrl('*/*');
                }
            }
        } catch (LocalizedException $e) {
            $resultData['error'] = true;
            $resultData['messages']['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error has occurred'));
            $resultData['redirect'] = $this->_url->getUrl('*/*');
            $this->logger->critical($e);
        }

        $resultJson->setData($resultData);

        return $resultJson;
    }
}
