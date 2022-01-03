<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Dropbox;

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
    const TYPE_ID = 'dropbox';
    const DATASCOPE = 'extension_attributes.dropbox_file_destination.';

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
        if (!$this->isLibExists()) {
            return $this->getNoticeMeta();
        }
        $tokenCommentLink = $arguments['tokenCommentLink']
            ?? 'https://amasty.com/docs/doku.php?id=magento_2:import_and_export#export_file_storage';

        return [
            'token' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Generated Access Token'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'input',
                            'elementTmpl' => 'Amasty_ExportPro/form/element/password',
                            'dataScope' => self::DATASCOPE . 'token',
                            'comment' => __('Please follow the ' .
                                '<a href="' . $tokenCommentLink . '" target="_blank">instructions</a> ' .
                                'from the user guide to generate access token.'),
                            'service' => ['template' => 'Amasty_ExportCore/form/element/service/comment'],
                            'validation' => [
                                'required-entry' => true
                            ],
                        ]
                    ]
                ]
            ],
            'file_path' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('File Path'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'file_path',
                            'notice' => 'File will be added into the folder specified above. However, in case '
                                . 'the specified folder does not exist a new folder will be created automatically.'
                        ]
                    ]
                ]
            ],
            'filename' => $this->filenameInput->get(
                'filename',
                self::DATASCOPE,
                __('File Name for Dropbox')->render()
            )
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['dropbox_file_destination'] ?? [];

        if (isset($requestConfig['token'])) {
            $config->setToken($requestConfig['token']);
        }

        if (isset($requestConfig['file_path'])) {
            $config->setFilePath($requestConfig['file_path']);
        }

        if (isset($requestConfig['filename'])) {
            $config->setFileName($requestConfig['filename']);
        }

        $profileConfig->getExtensionAttributes()->setDropboxFileDestination($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getDropboxFileDestination()) {
            return [
                'extension_attributes' => [
                    'dropbox_file_destination' => [
                        'token' => $config->getToken(),
                        'file_path' => $config->getFilePath(),
                        'filename' => $config->getFileName()
                    ]
                ]
            ];
        }

        return [];
    }

    private function isLibExists(): bool
    {
        try {
            $classExists = class_exists(\Spatie\Dropbox\Client::class);
        } catch (\Exception $e) {
            $classExists = false;
        }

        return $classExists;
    }

    private function getNoticeMeta(): array
    {
        return [
            'comment' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => null,
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'template' => 'Amasty_ExportPro/notice-field',
                            'additionalClasses' => 'amexport_output_notice',
                            'visible' => true,
                            'content' => __(
                                'PHP library <a href="https://github.com/spatie/dropbox-api" '
                                . ' target="_blank">Spatie</a> is not installed. Please install'
                                . ' the library to proceed with Dropbox storage.'
                            )
                        ],
                    ],
                ]
            ]
        ];
    }
}
