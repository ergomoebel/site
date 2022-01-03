<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


namespace Amasty\CronSchedule\Model\DataProvider;

use Amasty\CronSchedule\Api\Data\ScheduleInterface;
use Amasty\CronSchedule\Api\Data\ScheduleInterfaceFactory;
use Amasty\CronSchedule\Api\ScheduleRepositoryInterface;
use Magento\Framework\App\RequestInterface;

class Basic implements DataProviderInterface
{
    const ARGUMENT_LABEL = 'label';

    const DATA_SCOPE = 'schedule';
    const SCHEDULE = 'schedule';
    const ENABLED = 'enabled';
    const FREQUENCY = 'frequency';
    const CRON_FIELDS = ['minute', 'hour', 'day', 'month', 'day_of_week'];

    /**
     * @var ScheduleRepositoryInterface
     */
    private $scheduleRepository;

    /**
     * @var ScheduleInterfaceFactory
     */
    private $scheduleInterfaceFactory;

    /**
     * @var OptionSource\CronFrequency
     */
    private $cronFrequency;

    public function __construct(
        ScheduleRepositoryInterface $scheduleRepository,
        ScheduleInterfaceFactory $scheduleInterfaceFactory,
        OptionSource\CronFrequency $cronFrequency
    ) {
        $this->scheduleRepository = $scheduleRepository;
        $this->scheduleInterfaceFactory = $scheduleInterfaceFactory;
        $this->cronFrequency = $cronFrequency;
    }
    public function getMeta(string $jobType, array $arguments = [])
    {
        return array_merge_recursive(
            $this->getIsEnabledMeta($arguments[self::ARGUMENT_LABEL] ?? __('Enabled')),
            $this->getFrequencyContainer()
        );
    }

    public function getData(string $jobType, int $jobId = null)
    {
        $schedule = $this->scheduleRepository->getByJob($jobType, $jobId);
        if (!$schedule->getExpression()) {
            return [];
        }

        return [
            self::DATA_SCOPE => array_combine(
                self::CRON_FIELDS,
                explode(' ', $schedule->getExpression())
            ),
            self::FREQUENCY  => $schedule->getExpression(),
            self::ENABLED => $schedule->isEnabled() ? '1' : '0'
        ];
    }

    public function prepareSchedule(RequestInterface $request, string $jobType, ?int $jobId): ScheduleInterface
    {
        $schedule = $this->scheduleRepository->getByJob($jobType, $jobId);

        $data = $request->getParam(self::DATA_SCOPE);
        $schedule->setJobType($jobType);
        $schedule->setExternalId($jobId);
        $schedule->setIsEnabled((bool)$request->getParam(self::ENABLED));

        $expressionValues = [];
        foreach (self::CRON_FIELDS as $cronField) {
            $expressionValues[] = $data[$cronField] ?? '*';
        }
        $schedule->setExpression(implode(' ', $expressionValues));

        return $schedule;
    }

    public function getFrequencyContainer(): array
    {
        return [
            'frequency_container' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'         => '',
                            'visible'       => true,
                            'dataScope'     => '',
                            'componentType' => 'fieldset',
                            'additionalClasses' => 'amcronschedule-fieldset',
                        ]
                    ]
                ],
                'children'  => [
                    self::FREQUENCY => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label'         => __('Frequency'),
                                    'visible'       => true,
                                    'dataType'      => 'select',
                                    'dataScope'     => 'frequency',
                                    'additionalClasses' => 'amcron-select',
                                    'component'     => 'Amasty_CronSchedule/js/form/element/cron',
                                    'formElement'   => 'select',
                                    'componentType' => 'select',
                                    'options'       => $this->cronFrequency->toOptionArray()
                                ]
                            ]
                        ]
                    ],
                    self::SCHEDULE => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label'         => __('Schedule'),
                                    'visible'       => true,
                                    'formElement'   => 'container',
                                    'componentType' => 'container',
                                    'additionalClasses' => 'amcron-schedule-container',
                                    'dataScope'     => self::DATA_SCOPE,
                                    'component'     => 'Magento_Ui/js/form/components/group',
                                    'template'     => 'Amasty_CronSchedule/group/group',
                                    'tooltipTpl'     => 'ui/form/element/helper/tooltip',
                                    'tooltip'     => [
                                        'description' => __(
                                            'Read more about cron expressions ' .
                                            '- <a href="https://en.wikipedia.org/wiki/Cron"' .
                                            ' target="_blank" title="here">here</a>'
                                        )
                                    ],
                                    'breakLine'     => false,
                                ]
                            ]
                        ],
                        'children'  => $this->getScheduleFields(),
                    ]
                ]
            ]
        ];
    }

    public function getScheduleFields()
    {
        $result = [];
        $fields = array_combine(
            self::CRON_FIELDS,
            [
                __('Minutes'),
                __('Hours'),
                __('Days'),
                __('Month'),
                __('Days Of Week')
            ]
        );

        foreach ($fields as $cronField => $notice) {
            $result[$cronField] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'visible' => true,
                            'dataScope' => $cronField,
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'component' => 'Amasty_CronSchedule/js/form/element/input',
                            'additionalClasses' => 'admin__control-small',
                            'notice' => $notice,
                            'componentType' => 'input'
                        ]
                    ]
                ]
            ];
        }

        return $result;
    }

    public function getIsEnabledMeta($label): array
    {
        return [
            self::ENABLED => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'          => $label,
                            'dataType'       => 'boolean',
                            'prefer'         => 'toggle',
                            'sortOrder'      => 0,
                            'valueMap'       => ['true' => '1', 'false' => '0'],
                            'additionalClasses' => 'amcronschedule-field',
                            'default'        => 0,
                            'formElement'    => 'checkbox',
                            'visible'        => true,
                            'componentType'  => 'field',
                            'tooltip' => [
                                'description' => __(
                                    'If enabled, the export will be initiated automatically ' .
                                    'by cron according to the schedule specified.'
                                )
                            ],
                            'switcherConfig' => [
                                'enabled' => true,
                                'rules'   => [
                                    [
                                        'value'   => 0,
                                        'actions' => [
                                            [
                                                'target'   => 'index = frequency_container',
                                                'callback' => 'visible',
                                                'params'   => [false]
                                            ]
                                        ]
                                    ],
                                    [
                                        'value'   => 1,
                                        'actions' => [
                                            [
                                                'target'   => 'index = frequency_container',
                                                'callback' => 'visible',
                                                'params'   => [true]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
