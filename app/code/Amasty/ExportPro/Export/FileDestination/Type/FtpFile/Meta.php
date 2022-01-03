<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Export\FileDestination\Type\FtpFile;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Amasty\ExportCore\Export\Form\Filename\FilenameInput;
use Magento\Framework\App\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'ftp_file';
    const DATASCOPE = 'extension_attributes.ftp_file_destination.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var FilenameInput
     */
    private $filenameInput;

    public function __construct(
        ConfigInterfaceFactory $configFactory,
        FilenameInput $filenameInput
    ) {
        $this->configFactory = $configFactory;
        $this->filenameInput = $filenameInput;
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
                            'notice' => 'Add port if necessary (example.com:321).'
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
                            'formElement' => 'checkbox',
                            'prefer' => 'toggle',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'passive_mode',
                            'valueMap' => ['true' => 1, 'false' => 0],
                        ]
                    ]
                ]
            ],
            'user' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('User'),
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
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'elementTmpl' => 'Amasty_ExportPro/form/element/password',
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
            ],
            'filename' => $this->filenameInput->get(
                'filename',
                self::DATASCOPE,
                __('File Name for FTP')
            )
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['ftp_file_destination'] ?? [];
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

        if (isset($requestConfig['filename'])) {
            $config->setFilename($requestConfig['filename']);
        }

        $profileConfig->getExtensionAttributes()->setFtpFileDestination($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getFtpFileDestination()) {
            return [
                'extension_attributes' => [
                    'ftp_file_destination' => [
                        'host' => $config->getHost(),
                        'passive_mode' => (int)$config->isPassiveMode(),
                        'user' => $config->getUser(),
                        'password' => $config->getPassword(),
                        'path' => $config->getPath(),
                        'filename' => $config->getFilename()
                    ]
                ]
            ];
        }

        return [];
    }
}
