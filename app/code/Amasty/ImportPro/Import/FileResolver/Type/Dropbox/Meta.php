<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\Dropbox;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'dropbox_file';
    const DATASCOPE = 'extension_attributes.dropbox_file_resolver.';

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
        if (!$this->isLibExists()) {
            return $this->getNoticeMeta();
        }
        $tokenCommentLink = $arguments['tokenCommentLink']
            ?? 'https://amasty.com/docs/doku.php?id=magento_2:import_and_export#import_source';

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
                            'elementTmpl' => 'Amasty_ImportCore/form/element/password',
                            'dataScope' => self::DATASCOPE . 'token',
                            'comment' => __('Please follow the ' .
                                '<a href="' . $tokenCommentLink . '" target="_blank">instructions</a> ' .
                                'from the user guide to generate access token.'),
                            'service' => ['template' => 'Amasty_ImportPro/form/element/service/comment'],
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
                            'notice' => 'Specify the import file path on Dropbox, e.g. import/import_file.csv'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['dropbox_file_resolver'] ?? [];

        if (isset($requestConfig['token'])) {
            $config->setToken($requestConfig['token']);
        }

        if (isset($requestConfig['file_path'])) {
            $config->setFilePath($requestConfig['file_path']);
        }

        $profileConfig->getExtensionAttributes()->setDropboxFileResolver($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getDropboxFileResolver()) {
            return [
                'extension_attributes' => [
                    'dropbox_file_resolver' => [
                        'token' => $config->getToken(),
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
                            'template' => 'Amasty_ImportPro/notice-field',
                            'additionalClasses' => '-notice',
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
