<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Email;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Config\Model\Config\Source\Email\Identity;
use Amasty\ExportPro\Model\OptionSource\Email\Template;
use Magento\Framework\App\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'email';
    const DATASCOPE = 'extension_attributes.email_file_destination.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var Identity
     */
    private $identity;

    /**
     * @var Template
     */
    private $template;

    public function __construct(
        ConfigInterfaceFactory $configFactory,
        Identity $identity,
        Template $template
    ) {
        $this->configFactory = $configFactory;
        $this->identity = $identity;
        $this->template = $template;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        return [
            'sender' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Email Sender'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'dataType' => 'select',
                            'formElement' => 'select',
                            'visible' => true,
                            'componentType' => 'select',
                            'dataScope' => self::DATASCOPE . 'sender',
                            'options' => $this->identity->toOptionArray()
                        ]
                    ]
                ]
            ],
            'email_recipients' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('E-mail Recipients'),
                            'dataType' => 'input',
                            'formElement' => 'input',
                            'visible' => true,
                            'component' => 'Amasty_ExportPro/js/form/element/email',
                            'componentType' => 'input',
                            'dataScope' => self::DATASCOPE . 'email_recipients'
                        ]
                    ]
                ]
            ],
            'email_subject' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('E-mail Message Subject'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'email_subject'
                        ]
                    ]
                ]
            ],
            'template' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Email Template'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'dataType' => 'select',
                            'formElement' => 'select',
                            'visible' => true,
                            'componentType' => 'select',
                            'dataScope' => self::DATASCOPE . 'template',
                            'options' => $this->template->toOptionArray()
                        ]
                    ]
                ]
            ]
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();
        /** @var ProfileConfigInterface $profileConfig */
        $requestConfig = $request->getParam('extension_attributes')['email_file_destination'] ?? [];
        if (isset($requestConfig['sender'])) {
            $config->setSender($requestConfig['sender']);
        }

        if (isset($requestConfig['email_recipients'])) {
            $config->setEmailRecipients($requestConfig['email_recipients']);
        }

        if (isset($requestConfig['email_subject'])) {
            $config->setEmailSubject($requestConfig['email_subject']);
        }

        if (isset($requestConfig['template'])) {
            $config->setTemplate($requestConfig['template']);
        }

        $profileConfig->getExtensionAttributes()->setEmailFileDestination($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        /** @var ConfigInterface $config */
        if ($config = $profileConfig->getExtensionAttributes()->getEmailFileDestination()) {
            return [
                'extension_attributes' => [
                    'email_file_destination' => [
                        'sender' => $config->getSender(),
                        'email_recipients' => $config->getEmailRecipients(),
                        'email_subject' => $config->getEmailSubject(),
                        'template' => $config->getTemplate()
                    ]
                ]
            ];
        }

        return [];
    }
}
