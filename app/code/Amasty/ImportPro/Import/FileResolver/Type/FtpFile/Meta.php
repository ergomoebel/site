<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\FtpFile;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'ftp_file';
    const DATASCOPE = 'extension_attributes.ftp_file_resolver.';

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
                            'dataScope' => self::DATASCOPE . 'host',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
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
            'passive_mode' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Passive Mode'),
                            'dataType' => 'boolean',
                            'prefer' => 'toggle',
                            'valueMap' => ['true' => '1', 'false' => '0'],
                            'formElement' => 'checkbox',
                            'default' => '',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'passive_mode'
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
        $requestConfig = $request->getParam('extension_attributes')['ftp_file_resolver'] ?? [];
        if (isset($requestConfig['host'])) {
            $config->setHost($requestConfig['host']);
        }

        if (isset($requestConfig['passive_mode'])) {
            $config->setIsPassiveMode((bool)$requestConfig['passive_mode']);
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

        $profileConfig->getExtensionAttributes()->setFtpFileResolver($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getFtpFileResolver()) {
            return [
                'extension_attributes' => [
                    'ftp_file_resolver' => [
                        'host' => $config->getHost(),
                        'passive_mode' => $config->isPassiveMode(),
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
