<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Controller\Adminhtml\ExportJob;

use Amasty\CronSchedule\Model\DataProvider;
use Amasty\ExportCore\Export\FormProvider;
use Amasty\ExportPro\Api\CronJobRepositoryInterface;
use Amasty\ExportCore\Export\Config\ProfileConfigFactory;
use Amasty\ExportPro\Model\Job\DataProvider\CompositeFormType;
use Amasty\ExportPro\Model\Job\Job;
use Amasty\ExportPro\Model\ModuleType;
use Amasty\ImportExportCore\Utils\Serializer;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportPro\Model\Job\ScheduleConfig;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ExportPro::export_job_edit';

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
        FormProvider $formProvider,
        DataProvider $scheduleDataProvider,
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
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultData = [];

        try {
            $data = $this->getRequest()->getParam('encodedData');
            if (!empty($data)) {
                $params = $this->getRequest()->getParams();
                unset($params['encodedData']);
                $postData = \json_decode($data, true);
                $this->getRequest()->setParams(array_merge_recursive($params, $postData));
            }

            if ($data = $this->getRequest()->getParams()) {
                /** @var \Amasty\ExportCore\Export\Config\ProfileConfig $profileConfig */
                $profileConfig = $this->profileConfigFactory->create();
                $profileConfig->setStrategy('export');
                $profileConfig->setEntityCode($this->getRequest()->getParam('entity_code'));
                $this->formProvider->get(CompositeFormType::TYPE)->prepareConfig($profileConfig, $this->getRequest());

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
                $model->setIndexedStatus((int)$model->getSchedule()->isEnabled());
                $model->setProfileConfig($profileConfig);

                $this->jobRepository->save($model);
                $successMessage = __('You saved the profile.');
                if ($this->getRequest()->getParam('back')) {
                    if (!$jobId) {
                        $this->messageManager->addSuccessMessage($successMessage);
                        $resultData['redirect'] = $this->_url->getUrl(
                            '*/*/edit',
                            [Job::JOB_ID => $model->getJobId()]
                        );
                    } else {
                        $resultData['messages']['success'] = $successMessage;
                        if ($this->getRequest()->getParam('save_and_run')) {
                            $resultData['generate'] = true;
                        }
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
