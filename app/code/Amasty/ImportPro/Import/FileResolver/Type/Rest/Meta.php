<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\Rest;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Amasty\ImportPro\Import\FileResolver\Type\Rest\Auth\AuthConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'rest';
    const DATASCOPE = 'extension_attributes.rest_file_resolver.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

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
        AuthConfig $authConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->configFactory = $configFactory;
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
                            'additionalClasses' => 'amimportpro-auth_container',
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
                                    'additionalClasses' => 'amimportpro-auth',
                                    'componentType' => 'select',
                                    'caption' => __('No Auth'),
                                    'dataScope' => self::DATASCOPE . 'auth',
                                    'component' => 'Amasty_ImportCore/js/type-selector',
                                    'prefix' => 'auth_',
                                    'options' => [],
                                ]
                            ]
                        ]
                    ],
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
                    'additionalClasses' => 'amimportpro-auth-fieldset',
                    'component' => 'Amasty_ImportPro/js/form/components/fieldset',
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
        $requestConfig = $request->getParam('extension_attributes')['rest_file_resolver'] ?? [];
        if (isset($requestConfig['endpoint'])) {
            $config->setEndpoint($requestConfig['endpoint']);
        }

        if (isset($requestConfig['auth'])) {
            $config->setAuthType($requestConfig['auth']);
        }

        $profileConfig->getExtensionAttributes()->setRestFileResolver($config);

        if ($config->getAuthType() && !empty($this->authConfig->get($config->getAuthType())['metaClass'])) {
            $this->objectManager->create($this->authConfig->get($config->getAuthType())['metaClass'])
                ->prepareConfig($profileConfig, $request);
        }

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        /** @var ConfigInterface $config */
        if ($config = $profileConfig->getExtensionAttributes()->getRestFileResolver()) {
            $authData = [];
            if ($config->getAuthType() && !empty($this->authConfig->get($config->getAuthType())['metaClass'])) {
                $authData = $this->objectManager->create($this->authConfig->get($config->getAuthType())['metaClass'])
                    ->getData($profileConfig);
            }

            return array_merge(
                [
                    'extension_attributes' => [
                        'rest_file_resolver' => [
                            'endpoint'     => $config->getEndpoint(),
                            'auth'         => $config->getAuthType()
                        ]
                    ]
                ],
                $authData
            );
        }

        return [];
    }
}
