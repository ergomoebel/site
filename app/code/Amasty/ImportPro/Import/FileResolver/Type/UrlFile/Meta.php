<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\UrlFile;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'url_file';
    const DATASCOPE = 'extension_attributes.url_file_resolver.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    public function __construct(ConfigInterfaceFactory $configFactory)
    {
        $this->configFactory = $configFactory;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        return [
            'url' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('URL'),
                            'dataType' => 'text',
                            'validation' => [
                                'required-entry' => true,
                                'validate-url' => true
                            ],
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'url'
                        ]
                    ]
                ]
            ],
            'user' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Basic Authentication Username'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'user'
                        ]
                    ]
                ]
            ],
            'password' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Basic Authentication Password'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'elementTmpl' => 'Amasty_ImportCore/form/element/password',
                            'dataScope' => self::DATASCOPE . 'password'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['url_file_resolver'] ?? [];
        if (isset($requestConfig['url'])) {
            $config->setUrl($requestConfig['url']);
        }

        if (isset($requestConfig['user'])) {
            $config->setUser($requestConfig['user']);
        }

        if (isset($requestConfig['password'])) {
            $config->setPassword($requestConfig['password']);
        }

        $profileConfig->getExtensionAttributes()->setUrlFileResolver($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getUrlFileResolver()) {
            return [
                'extension_attributes' => [
                    'url_file_resolver' => [
                        'url' => $config->getUrl(),
                        'user' => $config->getUser(),
                        'password' => $config->getPassword(),
                    ]
                ]
            ];
        }

        return [];
    }
}
