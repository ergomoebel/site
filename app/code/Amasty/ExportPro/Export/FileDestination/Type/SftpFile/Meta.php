<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Export\FileDestination\Type\SftpFile;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FileDestination\FileDestinationMetaInterface;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;
use Amasty\ExportCore\Export\Form\Filename\FilenameInput;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'sftp_file';
    const DATASCOPE = 'extension_attributes.sftp_file_destination.';

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
            'user' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('User'),
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
                            'visible' => true,
                            'elementTmpl' => 'Amasty_ExportPro/form/element/password',
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
            ],
            'filename' => $this->filenameInput->get('filename', self::DATASCOPE, __('File Name for SFTP'))
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['sftp_file_destination'] ?? [];
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

        if (isset($requestConfig['filename'])) {
            $config->setFilename($requestConfig['filename']);
        }

        $profileConfig->getExtensionAttributes()->setSftpFileDestination($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getSftpFileDestination()) {
            return [
                'extension_attributes' => [
                    'sftp_file_destination' => [
                        'host' => $config->getHost(),
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
