<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Export\FileDestination\Type\GoogleDrive;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;
use Amasty\ExportCore\Export\Form\Filename\FilenameInput;
use Magento\Framework\UrlInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'drive_file';
    const DATASCOPE = 'extension_attributes.drive_file_destination.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var FilenameInput
     */
    private $filenameInput;

    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        ConfigInterfaceFactory $configFactory,
        FilenameInput $filenameInput,
        UrlInterface $url
    ) {
        $this->configFactory = $configFactory;
        $this->filenameInput = $filenameInput;
        $this->url = $url;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        if (!$this->isLibExists()) {
            return $this->getNoticeMeta();
        }
        $keyCommentLink = $arguments['keyCommentLink']
            ?? 'https://amasty.com/docs/doku.php?id=magento_2:import_and_export#export_file_storage';

        return [
            'upload_file' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Upload Service Account Key File'),
                            'btnLabel' => __('Select the File'),
                            'dataType' => 'text',
                            'validation' => [
                                'required-entry' => true
                            ],
                            'visible' => true,
                            'component' => 'Amasty_ExportPro/js/form/file-uploader',
                            'template' => 'Amasty_ExportPro/form/upload-file/upload',
                            'previewTmpl' => 'Amasty_ExportPro/form/upload-file/preview',
                            'componentType' => 'field',
                            'uploaderConfig' => [
                                'url' => $this->url->getUrl('amexportpro/drive/upload')
                            ],
                            'allowedExtensions' => 'json',
                            'deleteUrl' => $this->url->getUrl('amexportpro/drive/delete'),
                            'formElement' => 'fileUploader',
                            'dataScope' => self::DATASCOPE . 'upload_file.file',
                            'comment' => __('Please follow the ' .
                                '<a href="' . $keyCommentLink . '" target="_blank">instructions</a> ' .
                                'from the user guide to create a service account key.'),
                            'service' => ['template' => 'Amasty_ExportCore/form/element/service/comment'],
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
                            'validation' => [
                                'required-entry' => true
                            ],
                            'notice' => 'File will be added into the folder specified above.'
                        ]
                    ]
                ]
            ],
            'filename' => $this->filenameInput->get(
                'filename',
                self::DATASCOPE,
                __('File Name for Google Drive')->render()
            )
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['drive_file_destination'] ?? [];

        if (isset($requestConfig['upload_file']['file'][0]['name'])) {
            $config->setKey($requestConfig['upload_file']['file'][0]['name']);
        }

        if (isset($requestConfig['file_path'])) {
            $config->setFilePath($requestConfig['file_path']);
        }

        if (isset($requestConfig['filename'])) {
            $config->setFileName($requestConfig['filename']);
        }

        $profileConfig->getExtensionAttributes()->setDriveFileDestination($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getDriveFileDestination()) {
            return [
                'extension_attributes' => [
                    'drive_file_destination' => [
                        'upload_file' => [
                            'file' => [
                                ['name' => $config->getKey()]
                            ]
                        ],
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
            $classExists = class_exists(\Google_Service_Drive::class);
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
                                '<a href="https://github.com/googleapis/google-api-php-client" '
                                . ' target="_blank">Google APIs Client Library</a> for PHP is not installed. '
                                . 'Please install the library to proceed with Google Drive storage.'
                            )
                        ],
                    ],
                ]
            ]
        ];
    }
}
