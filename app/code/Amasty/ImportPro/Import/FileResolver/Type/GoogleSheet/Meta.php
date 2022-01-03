<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\GoogleSheet;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const TYPE_ID = 'google_sheet';
    const DATASCOPE = 'extension_attributes.google_sheet_file_resolver.';

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
                            'dataScope' => self::DATASCOPE . 'url',
                            'validation' => [
                                'required-entry' => true,
                                'validate-url' => true
                            ],
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();

        if (isset($request->getParam('extension_attributes')['google_sheet_file_resolver']['url'])) {
            $config->setUrl($request->getParam('extension_attributes')['google_sheet_file_resolver']['url']);
        }

        $profileConfig->getExtensionAttributes()->setGoogleSheetFileResolver($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getGoogleSheetFileResolver()) {
            return [
                'extension_attributes' => [
                    'google_sheet_file_resolver' => [
                        'url' => $config->getUrl()
                    ]
                ]
            ];
        }

        return [];
    }
}
