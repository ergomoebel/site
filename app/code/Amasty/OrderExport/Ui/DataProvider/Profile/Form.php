<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Ui\DataProvider\Profile;

use Amasty\CronSchedule\Model\DataProvider;
use Amasty\CronSchedule\Model\DataProvider\Basic;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Amasty\ExportCore\Export\FormProvider;
use Amasty\ImportExportCore\Utils\Serializer;
use Amasty\OrderExport\Api\ProfileRepositoryInterface;
use Amasty\OrderExport\Controller\Adminhtml\Profile\Duplicate;
use Amasty\OrderExport\Model\ModuleType;
use Amasty\OrderExport\Model\Profile\Profile;
use Amasty\OrderExport\Model\Profile\ResourceModel\CollectionFactory;
use Amasty\OrderExport\Model\Profile\ScheduleConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
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
     * @var Serializer
     */
    private $profileConfigSerializer;

    /**
     * @var DataProvider
     */
    private $scheduleDataProvider;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        ProfileRepositoryInterface $profileRepository,
        DataProvider $scheduleDataProvider,
        FormProvider $formProvider,
        Serializer $profileConfigSerializer,
        UrlInterface $url,
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
        $this->profileConfigSerializer = $profileConfigSerializer;
        $this->scheduleDataProvider = $scheduleDataProvider;
        $this->url = $url;
        $this->request = $request;
    }

    public function getData()
    {
        $data = parent::getData();

        if (!empty($data['items'])) {
            $profile = $this->profileRepository->getById((int)$data['items'][0]['id']);

            $profileConfig = $profile->getConfig();

            $exportEvents = $profile->getProfileEvents();
            $data[$profile->getId()] = array_merge_recursive(
                [
                    Profile::ID => !$this->request->getParam(Duplicate::REQUEST_PARAM_NAME)
                        ? $profile->getId()
                        : null,
                    'general' => [
                        Profile::NAME => $profile->getName(),
                        Profile::ORDER_ACTIONS => $this->getOrderActions($profile)
                    ],
                    'automatic_export' => [
                        'schedule_container' => $this->scheduleDataProvider->getData(
                            ModuleType::TYPE,
                            ScheduleConfig::DATAPROVIDER_TYPE,
                            $profile->getId()
                        ),
                        'export_events' => $exportEvents,
                        'enable_event' => !empty($exportEvents) ? '1' : '0'
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

        if ($entity = $this->entityConfigProvider->get('sales_order')) {
            $meta = array_merge_recursive(
                $meta,
                $this->formProvider->get(CompositeFormType::TYPE)->getMeta($entity)
            );
            $meta['automatic_export']['children']['schedule_container']['children'] =
                $this->scheduleDataProvider->getMeta(
                    ModuleType::TYPE,
                    [Basic::ARGUMENT_LABEL => __('Run Profile by Cron')],
                    ScheduleConfig::DATAPROVIDER_TYPE
                );
        }

        return $meta;
    }

    protected function getOrderActions(Profile $profile): array
    {
        $orderActions = [];

        foreach ($profile->getOrderActions() as $actionKey => $actionData) {
            switch ($actionKey) {
                case 'ship_order':
                    $orderActions['notify_customer_shipment'] = $actionData['options']['is_notify'] ?? 0;
                    break;
                case 'invoice_order':
                    $orderActions['notify_customer_invoice'] = $actionData['options']['is_notify'] ?? 0;
                    break;
            }
            $orderActions[$actionKey] = $actionData['options']['value'] ?? '';
        }

        return $orderActions;
    }
}
