<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Twig;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

/**
 * @codeCoverageIgnore
 */
class Meta implements FormInterface
{
    const DATASCOPE = 'extension_attributes.twig_template.';

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
        if (!$this->isLibExists()) {
            return $this->getNoticeMeta();
        }

        $twigTemplates = [];
        $twigTemplatesOptions = [];
        if (!empty($arguments['twigTemplates'])) {
            foreach ($arguments['twigTemplates'] as $twigTemplate) {
                if (\is_subclass_of($twigTemplate, TwigTemplateInterface::class)) {
                    $extension = $twigTemplate->getExtension();
                    $twigTemplatesOptions[] = ['label' => $twigTemplate->getName(), 'value' => $extension];
                    $twigTemplates[$extension] = [
                        'name' => $twigTemplate->getName(),
                        'template' => $extension,
                        'header' => $twigTemplate->getHeader(),
                        'content' => $twigTemplate->getContent(),
                        'separator' => $twigTemplate->getSeparator(),
                        'footer' => $twigTemplate->getFooter(),
                        'extension' => $extension
                    ];
                }
            }
        }

        return [
            'twig.template' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Template'),
                            'dataType' => 'select',
                            'formElement' => 'select',
                            'visible' => !empty($twigTemplatesOptions),
                            'component' => 'Amasty_ExportPro/js/form/element/template-selector',
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'template',
                            'twigTemplates' => $twigTemplates,
                            'options' => $twigTemplatesOptions,
                            'caption' => __('Please Select...')
                        ]
                    ]
                ]
            ],
            'twig.header' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Header'),
                            'dataType' => 'text',
                            'formElement' => 'textarea',
                            'additionalClasses' => 'amexportcore-textarea',
                            'visible' => true,
                            'default' => '',
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'header'
                        ]
                    ]
                ]
            ],
            'twig.content' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Content'),
                            'dataType' => 'text',
                            'formElement' => 'textarea',
                            'rows' => 20,
                            'component' => 'Amasty_ExportPro/js/form/element/codemirror',
                            'additionalClasses' => 'amexportcore-textarea -codemirror',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'content',
                            'notice' => __('{{ item }} is root entity variable')
                        ]
                    ]
                ]
            ],
            'twig.separator' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Item Delimiter'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'separator'
                        ]
                    ]
                ]
            ],
            'twig.footer' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Footer'),
                            'dataType' => 'text',
                            'formElement' => 'textarea',
                            'additionalClasses' => 'amexportcore-textarea',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'footer'
                        ]
                    ]
                ]
            ],
            'twig.extension' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Extension'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'extension'
                        ]
                    ]
                ]
            ],
        ];
    }

    public function isLibExists(): bool
    {
        try {
            $classExists = class_exists(\Twig\Environment::class);
        } catch (\Exception $e) {
            $classExists = false;
        }

        return $classExists;
    }

    protected function getNoticeMeta(): array
    {
        return [
            'comment' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => null,
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'component' => 'Magento_Ui/js/form/components/html',
                            'additionalClasses' => 'admin__fieldset-note',
                            'content' => __(
                                'PHP library <a href="https://twig.symfony.com/doc/3.x/installation.html"'
                                . ' target="_blank">Twig</a>'
                                . ' is not installed. Please install the library to proceed.'
                            )
                        ],
                    ],
                ]
            ]
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['twig_template'] ?? [];

        if (isset($requestConfig['template'])) {
            $config->setTemplate((string)$requestConfig['template']);
        }
        if (isset($requestConfig['header'])) {
            $config->setHeader((string)$requestConfig['header']);
        }
        if (isset($requestConfig['content'])) {
            $config->setContent((string)$requestConfig['content']);
        }
        if (isset($requestConfig['footer'])) {
            $config->setFooter((string)$requestConfig['footer']);
        }
        if (isset($requestConfig['separator'])) {
            $config->setSeparator((string)$requestConfig['separator']);
        }
        if (isset($requestConfig['extension'])) {
            $config->setExtension((string)$requestConfig['extension']);
        }

        $profileConfig->getExtensionAttributes()->setTwigTemplate($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getTwigTemplate()) {
            return [
                'extension_attributes' => [
                    'twig_template' => [
                        'template' => $config->getTemplate(),
                        'header' => $config->getHeader(),
                        'content' => $config->getContent(),
                        'footer' => $config->getFooter(),
                        'separator' => $config->getSeparator(),
                        'extension' => $config->getExtension(),
                    ]
                ]
            ];
        }

        return [];
    }
}
