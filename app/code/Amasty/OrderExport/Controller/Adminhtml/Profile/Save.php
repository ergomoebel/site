<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Controller\Adminhtml\Profile;

use Amasty\CronSchedule\Model\DataProvider;
use Amasty\ExportCore\Api\Config\ProfileConfigInterfaceFactory;
use Amasty\ExportCore\Export\FormProvider;
use Amasty\OrderExport\Api\Data\ProfileInterface;
use Amasty\OrderExport\Api\Data\ProfileInterfaceFactory;
use Amasty\OrderExport\Api\ProfileRepositoryInterface;
use Amasty\OrderExport\Model\ModuleType;
use Amasty\OrderExport\Model\OptionSource\ExecutionType;
use Amasty\OrderExport\Model\Profile\Profile;
use Amasty\OrderExport\Model\Profile\ScheduleConfig;
use Amasty\OrderExport\Ui\DataProvider\Profile\CompositeFormType;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderExport::order_export_profiles';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProfileConfigInterfaceFactory
     */
    private $profileConfigFactory;

    /**
     * @var FormProvider
     */
    private $formProvider;

    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var ProfileInterfaceFactory
     */
    private $profileFactory;

    /**
     * @var DataProvider
     */
    private $scheduleDataProvider;

    public function __construct(
        Action\Context $context,
        ProfileRepositoryInterface $profileRepository,
        ProfileInterfaceFactory $profileFactory,
        LoggerInterface $logger,
        FormProvider $formProvider,
        ProfileConfigInterfaceFactory $profileConfigFactory,
        DataProvider $scheduleDataProvider
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->profileConfigFactory = $profileConfigFactory;
        $this->formProvider = $formProvider;
        $this->profileRepository = $profileRepository;
        $this->profileFactory = $profileFactory;
        $this->scheduleDataProvider = $scheduleDataProvider;
    }

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
            if ($data = $this->getRequest()->getParam('general')) {
                /** @var \Amasty\ExportCore\Export\Config\ProfileConfig $profileConfig */
                $profileConfig = $this->profileConfigFactory->create();
                $profileConfig->setStrategy('export');
                $profileConfig->setEntityCode('sales_order');
                $this->formProvider->get(CompositeFormType::TYPE)->prepareConfig($profileConfig, $this->getRequest());

                if ($id = (int)$this->getRequest()->getParam(Profile::ID)) {
                    $model = $this->profileRepository->getById($id);
                } else {
                    /** @var ProfileInterface|Profile $model */
                    $model = $this->profileFactory->create();
                }
                $data['order_actions'] = $this->getOrderActions();
                $model->addData($data);

                $params = $this->getRequest()->getParams();
                $scheduleData = $params['automatic_export']['schedule_container'] ?? [];
                $this->getRequest()->setParams(array_merge_recursive($params, $scheduleData));

                $model->setSchedule(
                    $this->scheduleDataProvider->prepareSchedule(
                        ModuleType::TYPE,
                        ScheduleConfig::DATAPROVIDER_TYPE,
                        (int)$model->getId()
                    )
                );

                $exportEvents = [];
                $automaticExport = $this->getRequest()->getParam('automatic_export');
                if (!empty($automaticExport['enable_event'])
                    && !empty($automaticExport['export_events'])
                ) {
                    $exportEvents = $automaticExport['export_events'];
                }
                $model->setProfileEvents($exportEvents);
                $model->setConfig($profileConfig);
                $model->setFileFormat($profileConfig->getTemplateType());

                $schedule = $model->getSchedule()->isEnabled();
                $event = $automaticExport['enable_event'] ?? '';

                if ($event == 1 && $schedule !== false) {
                    $model->setExecutionType(ExecutionType::EVENT_AND_CRON);
                } elseif ($event == 1 && $schedule === false) {
                    $model->setExecutionType(ExecutionType::EVENT);
                } elseif ($schedule !== false && $event != 1) {
                    $model->setExecutionType(ExecutionType::CRON);
                } else {
                    $model->setExecutionType(ExecutionType::MANUAL);
                }

                $this->profileRepository->save($model);

                $successMessage = __('You saved the profile.');
                if ($this->getRequest()->getParam('back')) {
                    if (!$id) {
                        $this->messageManager->addSuccessMessage($successMessage);
                        $resultData['redirect'] = $this->_url->getUrl('*/*/edit', [Profile::ID => $model->getId()]);
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

    protected function getOrderActions()
    {
        $orderActions = $this->getRequest()->getParam(Profile::ORDER_ACTIONS, []);
        $formattedActions = [];
        $notifyInvoice = $orderActions['notify_customer_invoice'] ?? 0;
        $notifyShipment = $orderActions['notify_customer_shipment'] ?? 0;
        unset($orderActions['notify_customer_invoice']);
        unset($orderActions['notify_customer_shipment']);

        foreach ($orderActions as $key => $value) {
            $formattedActions[$key] = [
                'actionType' => $key,
                'options' => [
                    'value' => $value
                ]
            ];
            switch ($key) {
                case 'invoice_order':
                    $formattedActions[$key]['options']['is_notify'] = $notifyInvoice;
                    break;
                case 'ship_order':
                    $formattedActions[$key]['options']['is_notify'] = $notifyShipment;
                    break;
            }
        }

        return $formattedActions;
    }
}
