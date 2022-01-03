<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Xlsx;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository;

class Meta extends \Amasty\ExportPro\Export\Template\Type\Spout\Meta implements FormInterface
{
    const FORMAT = 'XLSX';
    const DATASCOPE = 'extension_attributes.xlsx_template.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var Repository
     */
    private $assetRepo;

    public function __construct(
        ConfigInterfaceFactory $configInterfaceFactory,
        Repository $assetRepo
    ) {
        $this->configFactory = $configInterfaceFactory;
        $this->assetRepo = $assetRepo;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        if (!$this->isLibExists()) {
            return $this->getNoticeMeta();
        }
        if (!empty($arguments['combineChildRowsImage'])) {
            $combineChildRowsImage = $this->assetRepo->getUrl($arguments['combineChildRowsImage']);
        } else {
            $combineChildRowsImage = $this->assetRepo->getUrl('Amasty_ExportCore::images/merge_rows.gif');
        }

        return [
            'xlsx.has_header_row' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Add Header Row'),
                            'dataType' => 'boolean',
                            'prefer' => 'toggle',
                            'dataScope' => self::DATASCOPE . 'has_header_row',
                            'valueMap' => ['true' => '1', 'false' => '0'],
                            'default' => '1',
                            'formElement' => 'checkbox',
                            'visible' => true,
                            'componentType' => 'field'
                        ]
                    ]
                ]
            ],
            'xlsx.combine_child_rows' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Merge Rows into One'),
                            'dataType' => 'boolean',
                            'prefer' => 'toggle',
                            'additionalClasses' => 'amexportcore-checkbox -type',
                            'dataScope' => self::DATASCOPE . 'combine_child_rows',
                            'valueMap' => ['true' => '1', 'false' => '0'],
                            'default' => '',
                            'formElement' => 'checkbox',
                            'visible' => true,
                            'componentType' => 'field',
                            'tooltipTpl' => 'Amasty_ExportCore/form/element/tooltip',
                            'tooltip' => [
                                'description' => '<img src="' . $combineChildRowsImage . '"/>'
                            ],
                            'notice' => __('Data from multiple rows will be merged into one cell, if enabled.'),
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
                            'label' => __('Entity Key Delimiter'),
                            'dataType' => 'text',
                            'default' => Config::SETTING_POSTFIX,
                            'visible' => true,
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'dataScope' => self::DATASCOPE . 'postfix',
                            'notice' => __('The character that separates the entity key from the column name.')
                        ]
                    ]
                ]
            ]
        ];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $config = $this->configFactory->create();
        $requestConfig = $request->getParam('extension_attributes')['xlsx_template'] ?? [];

        if (isset($requestConfig['has_header_row'])) {
            $config->setHasHeaderRow((bool)$requestConfig['has_header_row']);
        }
        if (isset($requestConfig['postfix'])) {
            $config->setPostfix((string)$requestConfig['postfix']);
        }
        if (isset($requestConfig['combine_child_rows'])) {
            $config->setCombineChildRows((bool)$requestConfig['combine_child_rows']);
            $config->setChildRowSeparator((string)$requestConfig['child_row_separator']);
        }

        $profileConfig->getExtensionAttributes()->setXlsxTemplate($config);

        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        if ($config = $profileConfig->getExtensionAttributes()->getXlsxTemplate()) {
            return [
                'extension_attributes' => [
                    'xlsx_template' => [
                        'has_header_row' => $config->isHasHeaderRow() ? '1' : '0',
                        'postfix' => $config->getPostfix(),
                        'combine_child_rows' => $config->isCombineChildRows() ? '1' : '0',
                        'child_row_separator' => $config->getChildRowSeparator(),
                    ]
                ]
            ];
        }

        return [];
    }
}
