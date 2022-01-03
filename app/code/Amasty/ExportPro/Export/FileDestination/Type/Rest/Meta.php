<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Rest;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Amasty\ExportPro\Export\FileDestination\Type\Rest\Auth\AuthConfig;
use Amasty\ExportPro\Export\FileDestination\Type\Rest\OptionSource\ContentType;
use Amasty\ExportPro\Export\FileDestination\Type\Rest\OptionSource\Methods;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'rest';
    const DATASCOPE = 'extension_attributes.rest_file_destination.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var Methods
     */
    private $methodOptions;

    /**
     * @var ContentType
     */
    private $contentTypeOptions;

    /**
     * @var AuthConfig
     */
    private $authConfig;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ConfigInterfaceFactory $configFactory,
        Methods $methodOptions,
        ContentType $contentTypeOptions,
        AuthConfig $authConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->configFactory = $configFactory;
        $this->methodOptions = $methodOptions;
        $this->contentTypeOptions = $contentTypeOptions;
        $this->authConfig = $authConfig;
        $this->objectManager = $objectManager;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = [
            'rest_file.endpoint' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Rest Api Endpoint'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'notice' => __('Ex.: https://magento.instance/rest/all/V1/some/endpoint'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'input',
                            'dataScope' => self::DATASCOPE . 'endpoint'
                        ]
                    ]
                ]
            ],
            'rest_file.auth_container' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => '',
                            'visible' => true,
                            'componentType' => 'fieldset',
                            'additionalClasses' => 'amexportpro-auth_container',
                            'dataScope' => '',
                        ]
                    ]
                ],
                'children' => [
                    'auth' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Auth'),
                                    'dataType' => 'select',
                                    'formElement' => 'select',
                                    'visible' => true,
                                    'componentType' => 'select',
                                    'caption' => __('No Auth'),
                                    'dataScope' => self::DATASCOPE . 'auth',
                                    'component' => 'Amasty_ExportCore/js/type-selector',
                                    'prefix' => 'auth_',
                                    'options' => [],
                                ]
                            ]
                        ]
                    ],
                ]
            ],
            'rest_file.method' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Method'),
                            'dataType' => 'select',
                            'formElement' => 'select',
                            'visible' => true,
                            'componentType' => 'select',
                            'dataScope' => self::DATASCOPE . 'method',
                            'options' => $this->methodOptions->toOptionArray(),
                        ]
                    ]
                ]
            ],
            'rest_file.content_type' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Content Type'),
                            'dataType' => 'select',
                            'formElement' => 'select',
                            'visible' => true,
                            'componentType' => 'select',
                            'dataScope' => self::DATASCOPE . 'content_type',
                            'options' => $this->contentTypeOptions->toOptionArray(),
                        ]
                    ]
                ]
            ]
        ];
        foreach ($this->authConfig->all() as $authConfig) {
            $result['rest_file.auth_container']['children']['auth']
            ['arguments']['data']['config']['options'][] = [
                'value' => $authConfig['code'],
                'label' => __($authConfig['name'])
            ];
            if (!empty($authConfig['metaClass'])) {
                $result['rest_file.auth_container']['children']['auth_' . $authConfig['code']]
                ['arguments']['data']['config'] = [
                    'label' => '',
                    'visible' => true,
                    'componentType' => 'fieldset',
                    'component' => 'Amasty_ExportPro/js/form/components/fieldset',
                    'prefix' => 'auth_',
                    'dataScope' => ''
                ];
                $result['rest_file.auth_container']['children']['auth_' . $authConfig['code']]
                ['children'] = $this->objectManager->create(
                    $authConfig['metaClass']
                )->getMeta($entityConfig, $arguments);
            }
        }

        return $result;
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();
        /** @var ProfileConfigInterface $profileConfig */
        $requestConfig = $request->getParam('extension_attributes')['rest_file_destination'] ?? [];
        if (isset($requestConfig['endpoint'])) {
            $config->setEndpoint($requestConfig['endpoint']);
        }

        if (isset($requestConfig['auth'])) {
            $config->setAuthType($requestConfig['auth']);
        }

        if (isset($requestConfig['method'])) {
            $config->setMethod((int)$requestConfig['method']);
        }

        if (isset($requestConfig['content_type'])) {
            $config->setContentType($requestConfig['content_type']);
        }

        $profileConfig->getExtensionAttributes()->setRestFileDestination($config);

        if ($config->getAuthType() && !empty($this->authConfig->get($config->getAuthType())['metaClass'])) {
            $this->objectManager->create($this->authConfig->get($config->getAuthType())['metaClass'])
                ->prepareConfig($profileConfig, $request);
        }

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        /** @var ConfigInterface $config */
        if ($config = $profileConfig->getExtensionAttributes()->getRestFileDestination()) {
            $authData = [];
            if ($config->getAuthType() && !empty($this->authConfig->get($config->getAuthType())['metaClass'])) {
                $authData = $this->objectManager->create($this->authConfig->get($config->getAuthType())['metaClass'])
                    ->getData($profileConfig);
            }

            return array_merge(
                [
                    'extension_attributes' => [
                        'rest_file_destination' => [
                                'endpoint' => $config->getEndpoint(),
                                'auth' => $config->getAuthType(),
                                'method' => $config->getMethod(),
                                'content_type' => $config->getContentType(),
                        ]
                    ]
                ],
                $authData
            );
        }

        return [];
    }
}
