<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Model\Job\DataProvider;

use Amasty\CronSchedule\Model\DataProvider;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Amasty\ExportCore\Export\FormProvider;
use Amasty\ExportPro\Api\CronJobRepositoryInterface;
use Amasty\ExportPro\Model\Job\Job;
use Amasty\ExportPro\Model\Job\ResourceModel\Collection;
use Amasty\ExportPro\Model\Job\ResourceModel\CollectionFactory;
use Amasty\ExportPro\Model\Job\ScheduleConfig;
use Amasty\ExportPro\Model\ModuleType;
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
     * @var DataProvider
     */
    private $scheduleDataProvider;

    /**
     * @var int
     */
    private $jobId;

    /**
     * @var string
     */
    private $selectedEntityCode;

    /**
     * @var array
     */
    private $formData;

    /**
     * @var CronJobRepositoryInterface
     */
    private $cronJobRepository;

    /**
     * @var FormProvider
     */
    private $formProvider;

    public function __construct(
        CollectionFactory $collectionFactory,
        EntityConfigProvider $entityConfigProvider,
        RequestInterface $request,
        DataProvider $scheduleDataProvider,
        CronJobRepositoryInterface $cronJobRepository,
        FormProvider $formProvider,
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
        $this->scheduleDataProvider = $scheduleDataProvider;
        $this->cronJobRepository = $cronJobRepository;
        $this->formProvider = $formProvider;
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
        $entityCodeElement['options'] = [];

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

            if (!empty($selectedEntity->getDescription())) {
                $entityCodeElement['config']['notice'] = $selectedEntity->getDescription();
            }
            $meta['general']['children']['schedule_container']['children'] = $this->scheduleDataProvider->getMeta(
                ModuleType::TYPE,
                [],
                ScheduleConfig::DATAPROVIDER_TYPE
            );
            $meta = array_merge_recursive(
                $meta,
                $this->formProvider->get(CompositeFormType::TYPE)->getMeta($selectedEntity)
            );
        }

        return $meta;
    }
}
