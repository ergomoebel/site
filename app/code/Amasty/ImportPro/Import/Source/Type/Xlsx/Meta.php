<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\Source\Type\Xlsx;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Amasty\ImportCore\Api\Source\SourceConfigInterface;
use Amasty\ImportPro\Import\Source\Type\Spout\AbstractMeta;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class Meta extends AbstractMeta
{
    const DATASCOPE = 'extension_attributes.xlsx_source.';
    const FORMAT = 'XLSX';

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    public function __construct(
        SourceConfigInterface $sourceConfig,
        UrlInterface $url,
        ConfigInterfaceFactory $configFactory
    ) {
        parent::__construct($sourceConfig, $url);
        $this->configFactory = $configFactory;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        if (!$this->isLibExists()) {
            return $this->getNoticeMeta();
        }

        $result = [
            'xlsx.combine_child_rows' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Rows Merged into One'),
                            'dataType' => 'boolean',
                            'prefer' => 'toggle',
                            'dataScope' => self::DATASCOPE . 'combine_child_rows',
                            'valueMap' => ['true' => '1', 'false' => '0'],
                            'default' => '',
                            'formElement' => 'checkbox',
                            'visible' => true,
                            'sortOrder' => 20,
                            'componentType' => 'field',
                            'notice' => __(
                                'Please enable the setting if you have data from multiple rows merged into one cell.'
                            ),
                            'switcherConfig' => [
                                'enabled' => true,
                                'rules'   => [
                                    [
                                        'value'   => 0,
                                        'actions' => [
                                            [
                                                'target'   => 'index = xlsx.child_rows.delimiter',
                                                'callback' => 'visible',
                                                'params'   => [false]
                                            ]
                                        ]
                                    ],
                                    [
                                        'value'   => 1,
                                        'actions' => [
                                            [
                                                'target'   => 'index = xlsx.child_rows.delimiter',
                                                'callback' => 'visible',
                                                'params'   => [true]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'xlsx.child_rows.delimiter' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Merged Rows Data Delimiter'),
                            'dataType' => 'text',
                            'default' => Config::SETTING_CHILD_ROW_SEPARATOR,
                            'formElement' => 'input',
                            'visible' => true,
                            'sortOrder' => 30,
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'child_row_separator',
                            'validation' => [
                                'required-entry' => true
                            ],
                            'notice' => __('The character that delimits each field of the child rows.')
                        ]
                    ]
                ]
            ],
            'xlsx.postfix' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Prefix/Tag Delimiter'),
                            'dataType' => 'text',
                            'default' => Config::SETTING_PREFIX,
                            'visible' => true,
                            'sortOrder' => 40,
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'postfix',
                            'notice' => __('The character that separates the prefix/tag from the column name.')
                        ]
                    ]
                ]
            ]
        ];

        $result = array_merge_recursive(
            $result,
            $this->getSampleLinkMeta($entityConfig, Reader::TYPE_ID)
        );

        return $result;
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['xlsx_source'] ?? [];

        if (isset($requestConfig['combine_child_rows'])) {
            $config->setCombineChildRows((bool)$requestConfig['combine_child_rows']);
            $config->setChildRowSeparator((string)$requestConfig['child_row_separator']);
        }
        if (isset($requestConfig['postfix'])) {
            $config->setPrefix((string)$requestConfig['postfix']);
        }
        $profileConfig->getExtensionAttributes()->setXlsxSource($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getXlsxSource()) {
            return [
                'extension_attributes' => [
                    'xlsx_source' => [
                        'prefix' => $config->getPrefix(),
                        'combine_child_rows' => $config->isCombineChildRows() ? '1' : '0',
                        'child_row_separator' => $config->getChildRowSeparator(),
                    ]
                ]
            ];
        }

        return [];
    }
}
