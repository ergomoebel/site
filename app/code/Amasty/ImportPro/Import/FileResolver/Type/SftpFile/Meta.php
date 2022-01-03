<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\SftpFile;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'sftp_file';
    const DATASCOPE = 'extension_attributes.sftp_file_resolver.';

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
            'host' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Host'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'host',
                            'notice' => 'Add port if necessary (example.com:321)'
                        ]
                    ]
                ]
            ],
            'user' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('User Name'),
                            'validation' => [
                                'required-entry' => true
                            ],
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
                            'label' => __('Password'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'elementTmpl' => 'Amasty_ImportCore/form/element/password',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'password'
                        ]
                    ]
                ]
            ],
            'path' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('File Path'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'path'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['sftp_file_resolver'] ?? [];
        if (isset($requestConfig['host'])) {
            $config->setHost($requestConfig['host']);
        }

        if (isset($requestConfig['user'])) {
            $config->setUser($requestConfig['user']);
        }

        if (isset($requestConfig['password'])) {
            $config->setPassword($requestConfig['password']);
        }

        if (isset($requestConfig['path'])) {
            $config->setPath($requestConfig['path']);
        }

        $profileConfig->getExtensionAttributes()->setSftpFileResolver($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getSftpFileResolver()) {
            return [
                'extension_attributes' => [
                    'sftp_file_resolver' => [
                        'host' => $config->getHost(),
                        'user' => $config->getUser(),
                        'password' => $config->getPassword(),
                        'path' => $config->getPath()
                    ]
                ]
            ];
        }

        return [];
    }
}
