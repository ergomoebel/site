<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\GoogleDrive;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'drive_file';
    const DATASCOPE = 'extension_attributes.drive_file_resolver.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        ConfigInterfaceFactory $configFactory,
        UrlInterface $url
    ) {
        $this->configFactory = $configFactory;
        $this->url = $url;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        if (!$this->isLibExists()) {
            return $this->getNoticeMeta();
        }
        $keyCommentLink = $arguments['keyCommentLink']
            ?? 'https://amasty.com/docs/doku.php?id=magento_2:import_and_export#import_source';

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
                            'component' => 'Amasty_ImportPro/js/form/file-uploader',
                            'template' => 'Amasty_ImportCore/upload-file/upload',
                            'previewTmpl' => 'Amasty_ImportCore/upload-file/preview',
                            'componentType' => 'field',
                            'uploaderConfig' => [
                                'url' => $this->url->getUrl('amimportpro/drive/upload')
                            ],
                            'allowedExtensions' => 'json',
                            'deleteUrl' => $this->url->getUrl('amimportpro/drive/delete'),
                            'formElement' => 'fileUploader',
                            'dataScope' => self::DATASCOPE . 'upload_file.file',
                            'comment' => __('Please follow the ' .
                                '<a href="' . $keyCommentLink . '" target="_blank">instructions</a> ' .
                                'from the user guide to create a service account key.'),
                            'service' => ['template' => 'Amasty_ImportPro/form/element/service/comment'],
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
                            'notice' => 'Specify the import file path on Google Drive, e.g. import/import_file.csv'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['drive_file_resolver'] ?? [];

        if (isset($requestConfig['upload_file']['file'][0]['name'])) {
            $config->setKey($requestConfig['upload_file']['file'][0]['name']);
        }

        if (isset($requestConfig['file_path'])) {
            $config->setFilePath($requestConfig['file_path']);
        }

        $profileConfig->getExtensionAttributes()->setDriveFileResolver($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getDriveFileResolver()) {
            return [
                'extension_attributes' => [
                    'drive_file_resolver' => [
                        'upload_file' => [
                            'file' => [
                                ['name' => $config->getKey()]
                            ]
                        ],
                        'file_path' => $config->getFilePath(),
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
                            'template' => 'Amasty_ImportPro/notice-field',
                            'additionalClasses' => '-notice',
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
