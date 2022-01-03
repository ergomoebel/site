<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Model\Job\DataProvider;

use Amasty\CronSchedule\Model\DataProvider;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\FormProvider;
use Amasty\ImportExportCore\Utils\Serializer;
use Amasty\ImportPro\Api\CronJobRepositoryInterface;
use Amasty\ImportPro\Model\Job\Job;
use Amasty\ImportPro\Model\Job\ResourceModel\Collection;
use Amasty\ImportPro\Model\Job\ResourceModel\CollectionFactory;
use Amasty\ImportPro\Model\Job\ScheduleConfig;
use Amasty\ImportPro\Model\ModuleType;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Form extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var Serializer
     */
    private $profileConfigSerializer;

    /**
     * @var DataProvider
     */
    private $scheduleDataProvider;

    /**
     * @var FormProvider
     */
    private $formProvider;

    /**
     * @var array
     */
    private $formData;

    /**
     * @var CronJobRepositoryInterface
     */
    private $cronJobRepository;

    /**
     * @var int
     */
    private $jobId;

    /**
     * @var string
     */
    private $selectedEntityCode;

    public function __construct(
        CollectionFactory $collectionFactory,
        EntityConfigProvider $entityConfigProvider,
        RequestInterface $request,
        FormProvider $formProvider,
        Serializer $profileConfigSerializer,
        DataProvider $scheduleDataProvider,
        CronJobRepositoryInterface $cronJobRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->entityConfigProvider = $entityConfigProvider;
        $this->jobId = (int)$request->getParam($requestFieldName);
        $this->selectedEntityCode = $request->getParam('entity_code');
        $this->profileConfigSerializer = $profileConfigSerializer;
        $this->scheduleDataProvider = $scheduleDataProvider;
        $this->formProvider = $formProvider;
        $this->cronJobRepository = $cronJobRepository;
    }

    public function getData()
    {
        if ($this->formData === null) {
            $this->formData = [];
            if (!empty($this->jobId)) {
                try {
                    $job = $this->cronJobRepository->getById($this->jobId);

                    $this->formData[$job->getJobId()] = array_merge(
                        [
                            Job::JOB_ID => $job->getJobId(),
                            Job::ENTITY_CODE => $job->getEntityCode(),
                            Job::TITLE => $job->getTitle()
                        ],
                        $this->formProvider->get(CompositeFormType::TYPE)->getData($job->getProfileConfig()),
                        $this->scheduleDataProvider->getData(
                            ModuleType::TYPE,
                            ScheduleConfig::DATAPROVIDER_TYPE,
                            $job->getJobId()
                        )
                    );
                } catch (NoSuchEntityException $e) {
                    $this->jobId = 0;
                }
            }
        }

        return $this->formData;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
        $entityCodeElement = &$meta['general']['children']['entity_code']['arguments']['data'];

        if ($this->jobId) {
            $this->getData();
            $selectedEntity = $this->entityConfigProvider->get($this->formData[$this->jobId][Job::ENTITY_CODE]);
        } elseif ($this->selectedEntityCode) {
            $selectedEntity = $this->entityConfigProvider->get($this->selectedEntityCode);
        } else {
            throw new LocalizedException(__('Unexpected situation'));
        }

        if ($selectedEntity) {
            $entityCodeElement['options'][] = [
                'label' => $selectedEntity->getName(),
                'value' => $selectedEntity->getEntityCode()
            ];
            $entityCodeElement['config']['disabled'] = true;
            $entityCodeElement['config']['value'] = $selectedEntity->getEntityCode();

            if ($selectedEntity->getDescription()) {
                $entityCodeElement['config']['notice'] = $selectedEntity->getDescription();
            }
            $meta = array_merge_recursive(
                $meta,
                $this->formProvider->get(CompositeFormType::TYPE)->getMeta($selectedEntity)
            );
            $meta['general']['children']['schedule_container']['children'] = $this->scheduleDataProvider->getMeta(
                ModuleType::TYPE,
                [],
                ScheduleConfig::DATAPROVIDER_TYPE
            );
        }

        return $meta;
    }
}
