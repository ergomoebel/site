<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Ui\DataProvider\Profile;

use Amasty\CronSchedule\Model\DataProvider;
use Amasty\CronSchedule\Model\DataProvider\Basic;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\FormProvider;
use Amasty\ProductImport\Api\ProfileRepositoryInterface;
use Amasty\ProductImport\Controller\Adminhtml\Profile\Duplicate;
use Amasty\ProductImport\Controller\Adminhtml\Profile\Save as SaveController;
use Amasty\ProductImport\Model\ModuleType;
use Amasty\ProductImport\Model\Profile\Profile;
use Amasty\ProductImport\Model\Profile\ResourceModel\CollectionFactory;
use Amasty\ProductImport\Model\Profile\ScheduleConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Form extends AbstractDataProvider
{
    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var FormProvider
     */
    private $formProvider;

    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var DataProvider
     */
    private $scheduleDataProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        ProfileRepositoryInterface $profileRepository,
        DataProvider $scheduleDataProvider,
        FormProvider $formProvider,
        RequestInterface $request,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->entityConfigProvider = $entityConfigProvider;
        $this->formProvider = $formProvider;
        $this->profileRepository = $profileRepository;
        $this->scheduleDataProvider = $scheduleDataProvider;
        $this->request = $request;
    }

    public function getData()
    {
        $data = parent::getData();

        if (!empty($data['items'])) {
            $profile = $this->profileRepository->getById((int)$data['items'][0]['id']);

            $profileConfig = $profile->getConfig();

            $data[$profile->getId()] = array_merge_recursive(
                [
                    'general' => array_merge(
                        [Profile::NAME => $profile->getName()],
                        $this->getProductActions($profile)
                    ),
                    Profile::ID => !$this->request->getParam(Duplicate::REQUEST_PARAM_NAME)
                        ? $profile->getId()
                        : null,
                    'automatic_import' => [
                        'schedule_container' => $this->scheduleDataProvider->getData(
                            ModuleType::TYPE,
                            ScheduleConfig::DATAPROVIDER_TYPE,
                            $profile->getId()
                        )
                    ]
                ],
                $this->formProvider->get(CompositeFormType::TYPE)->getData($profileConfig)
            );
        }

        return $data;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();

        if ($entity = $this->entityConfigProvider->get('catalog_product_entity')) {
            $meta = array_merge_recursive(
                $meta,
                $this->formProvider->get(CompositeFormType::TYPE)->getMeta($entity)
            );
            $meta['automatic_import']['children']['schedule_container']['children'] =
                $this->scheduleDataProvider->getMeta(
                    ModuleType::TYPE,
                    [Basic::ARGUMENT_LABEL => __('Run Profile by Cron')],
                    ScheduleConfig::DATAPROVIDER_TYPE
                );
            $meta['automatic_import']['children']['schedule_container']['children']
            ['enabled']['arguments']['data']['config']['tooltip']['description'] = __(
                'If enabled, the import will be initiated automatically by cron according to the schedule specified.'
            );
        }

        return $meta;
    }

    /**
     * Get product actions data
     *
     * @param Profile $profile
     * @return array
     */
    protected function getProductActions(Profile $profile): array
    {
        $productActions = [];

        foreach ($profile->getProductActions() as $actionKey => $actionData) {
            $productActions[$actionKey] = $actionData['options']['value'] ?? '';
            foreach ((SaveController::ACTIONS_OPTION_FIELDS[$actionKey] ?? []) as $actionOption) {
                if (isset($actionData['options'][$actionOption])
                    && $actionData['options'][$actionOption] !== ''
                ) {
                    $productActions[$actionOption] = $actionData['options'][$actionOption];
                }
            }
        }

        return $productActions;
    }
}
