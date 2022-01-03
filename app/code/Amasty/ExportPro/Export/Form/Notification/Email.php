<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Form\Notification;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Amasty\ExportPro\Export\Notification\Type\Email\ConfigFactory;
use Magento\Config\Model\Config\Source;
use Magento\Framework\App\RequestInterface;

class Email implements FormInterface
{
    const DATASCOPE = 'email_notification_config.';

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @var Source\Email\Identity
     */
    private $emailIdentity;

    /**
     * @var Source\Email\Template
     */
    private $emailTemplate;

    public function __construct(
        ConfigFactory $configFactory,
        Source\Email\Identity $emailIdentity,
        Source\Email\Template $emailTemplate
    ) {
        $this->configFactory = $configFactory;
        $this->emailIdentity = $emailIdentity;
        $this->emailTemplate = $emailTemplate;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        return [
            'email_notifications' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => ($arguments['label'] ?? __('Email Alert for Export Error')),
                            'componentType' => 'fieldset',
                            'visible' => true,
                            'collapsible' => true,
                            'opened' => true,
                            'template' => 'Amasty_ExportCore/form/fieldset',
                        ]
                    ]
                ],
                'children' => [
                    'email_alert_enabled' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Enable Email Alert'),
                                    'dataType' => 'boolean',
                                    'prefer' => 'toggle',
                                    'dataScope' => self::DATASCOPE . 'email_alert_enabled',
                                    'valueMap' => ['true' => '1', 'false' => '0'],
                                    'default' => '',
                                    'formElement' => 'checkbox',
                                    'visible' => true,
                                    'sortOrder' => 20,
                                    'componentType' => 'field',
                                    'tooltip' => [
                                        'description' => ($arguments['enable_tooltip']
                                            ?? __(
                                                'Emails will be sent to recipients when an error happens in export.'
                                            )
                                        ),
                                    ],
                                    'switcherConfig' => [
                                        'enabled' => true,
                                        'rules'   => [
                                            [
                                                'value'   => 0,
                                                'actions' => [
                                                    [
                                                        'target'   => 'index = email_alert_sender',
                                                        'callback' => 'visible',
                                                        'params'   => [false]
                                                    ],
                                                    [
                                                        'target'   => 'index = email_alert_recipients',
                                                        'callback' => 'visible',
                                                        'params'   => [false]
                                                    ],
                                                    [
                                                        'target'   => 'index = email_alert_template',
                                                        'callback' => 'visible',
                                                        'params'   => [false]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'value'   => 1,
                                                'actions' => [
                                                    [
                                                        'target'   => 'index = email_alert_sender',
                                                        'callback' => 'visible',
                                                        'params'   => [true]
                                                    ],
                                                    [
                                                        'target'   => 'index = email_alert_recipients',
                                                        'callback' => 'visible',
                                                        'params'   => [true]
                                                    ],
                                                    [
                                                        'target'   => 'index = email_alert_template',
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
                    ],
                    'email_alert_sender' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Email Sender'),
                                    'componentType' => 'select',
                                    'visible' => true,
                                    'dataScope' => self::DATASCOPE . 'email_alert_sender',
                                    'options' => $this->emailIdentity->toOptionArray()
                                ]
                            ]
                        ],
                    ],
                    'email_alert_recipients' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Email Recipients'),
                                    'componentType' => 'field',
                                    'visible' => true,
                                    'dataType' => 'text',
                                    'formElement' => 'input',
                                    'dataScope' => self::DATASCOPE . 'email_alert_recipients',
                                    'component' => 'Amasty_ExportPro/js/form/element/email',
                                    'extraClasses' => 'admin__control-text',
                                ]
                            ]
                        ],
                    ],
                    'email_alert_template' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Email Template'),
                                    'componentType' => 'select',
                                    'visible' => true,
                                    'dataScope' => self::DATASCOPE . 'email_alert_template',
                                    'options' => $this->emailTemplate->toOptionArray()
                                ]
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($notificationConfig = $profileConfig->getExtensionAttributes()->getEmailNotificationConfig()) {
            return [
                'email_notifications' => [
                    'email_notification_config' => [
                        'email_alert_enabled' => (string)$notificationConfig->isAlertEnabled(),
                        'email_alert_sender' => $notificationConfig->getAlertSender(),
                        'email_alert_recipients' => implode(',', $notificationConfig->getAlertRecipients() ?? []),
                        'email_alert_template' => $notificationConfig->getAlertTemplate(),
                    ]
                ]
            ];
        }

        return [];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $emailNotifications = $request->getParam('email_notifications', [])['email_notification_config'] ?? [];
        $notificationConfig = $this->configFactory->create();
        $notificationConfig->setIsAlertEnabled((bool)$emailNotifications['email_alert_enabled'] ?? false)
            ->setAlertSender($emailNotifications['email_alert_sender'] ?? '')
            ->setAlertRecipients(explode(',', $emailNotifications['email_alert_recipients'] ?? ''))
            ->setAlertTemplate($emailNotifications['email_alert_template'] ?? '');
        $profileConfig->getExtensionAttributes()->setEmailNotificationConfig($notificationConfig);

        return $this;
    }
}
