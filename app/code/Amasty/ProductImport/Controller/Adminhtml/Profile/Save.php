<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Controller\Adminhtml\Profile;

use Amasty\CronSchedule\Model\DataProvider;
use Amasty\ImportCore\Api\Config\ProfileConfigInterfaceFactory;
use Amasty\ImportCore\Import\FormProvider;
use Amasty\ProductImport\Api\Data\ProfileInterface;
use Amasty\ProductImport\Api\Data\ProfileInterfaceFactory;
use Amasty\ProductImport\Api\ProfileRepositoryInterface;
use Amasty\ProductImport\Model\ModuleType;
use Amasty\ProductImport\Model\OptionSource\ExecutionType;
use Amasty\ProductImport\Model\Profile\Profile;
use Amasty\ProductImport\Model\Profile\ScheduleConfig;
use Amasty\ProductImport\Ui\DataProvider\Profile\CompositeFormType;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    const ACTIONS_OPTION_FIELDS = [
        'disable_products' => [],
        'reindex' => []
    ];

    const ADMIN_RESOURCE = 'Amasty_ProductImport::product_import_profiles';

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
                /** @var \Amasty\ImportCore\Import\Config\ProfileConfig $profileConfig */
                $profileConfig = $this->profileConfigFactory->create();
                $profileConfig->setStrategy('validate_and_import');
                $profileConfig->setEntityCode('catalog_product_entity');
                $this->formProvider->get(CompositeFormType::TYPE)->prepareConfig($profileConfig, $this->getRequest());

                if ($id = (int)$this->getRequest()->getParam(Profile::ID)) {
                    $model = $this->profileRepository->getById($id);
                } else {
                    /** @var ProfileInterface|Profile $model */
                    $model = $this->profileFactory->create();
                }
                $data['product_actions'] = $this->getProductActions();
                $model->addData($data);

                $params = $this->getRequest()->getParams();
                $scheduleData = $params['automatic_import']['schedule_container'] ?? [];
                $this->getRequest()->setParams(array_merge_recursive($params, $scheduleData));

                $model->setSchedule(
                    $this->scheduleDataProvider->prepareSchedule(
                        ModuleType::TYPE,
                        ScheduleConfig::DATAPROVIDER_TYPE,
                        (int)$model->getId()
                    )
                );

                $model->setConfig($profileConfig);
                $model->setSourceType($profileConfig->getSourceType());

                $schedule = $model->getSchedule()->isEnabled();
                if ($schedule !== false) {
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
                            $resultData['import'] = true;
                        }
                        if ($this->getRequest()->getParam('save_and_validate')) {
                            $resultData['validate'] = true;
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

    /**
     * Get formatted product actions
     *
     * @return array
     */
    private function getProductActions()
    {
        $productActions = $this->getRequest()->getParam('general') ?? [];
        $formattedActions = [];

        foreach ($productActions as $key => $value) {
            if (!key_exists($key, self::ACTIONS_OPTION_FIELDS)) {
                continue;
            }
            $formattedActions[$key] = [
                'actionType' => $key,
                'options' => [
                    'value' => $value
                ]
            ];

            foreach (self::ACTIONS_OPTION_FIELDS[$key] as $option) {
                $formattedActions[$key]['options'][$option] = $productActions[$option] ?? '';
            }
        }

        return $formattedActions;
    }
}
