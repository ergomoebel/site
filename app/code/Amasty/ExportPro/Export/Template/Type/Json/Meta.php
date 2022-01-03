<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Json;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const DATASCOPE = 'extension_attributes.json_template.';

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    public function __construct(ConfigInterfaceFactory $configFactory)
    {
        $this->configFactory = $configFactory;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        return [
            'json.header' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Header'),
                            'dataType' => 'text',
                            'formElement' => 'textarea',
                            'additionalClasses' => 'amexportcore-textarea',
                            'visible' => true,
                            'default' => '[',
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'header'
                        ]
                    ]
                ]
            ],
            'json.footer' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Footer'),
                            'dataType' => 'text',
                            'formElement' => 'textarea',
                            'additionalClasses' => 'amexportcore-textarea',
                            'default' => ']',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'footer'
                        ]
                    ]
                ]
            ],
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['json_template'] ?? [];
        if (isset($requestConfig['header'])) {
            $config->setHeader((string)$requestConfig['header']);
        }
        if (isset($requestConfig['footer'])) {
            $config->setFooter((string)$requestConfig['footer']);
        }

        $profileConfig->getExtensionAttributes()->setJsonTemplate($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getJsonTemplate()) {
            return [
                'extension_attributes' => [
                    'json_template' => [
                        'header' => $config->getHeader(),
                        'footer' => $config->getFooter()
                    ]
                ]
            ];
        }

        return [];
    }
}
