<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


namespace Amasty\ProductImport\Import\Form;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Amasty\ImportCore\Import\Form\CompositeForm;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository;

class Fields extends CompositeForm implements FormInterface
{
    /**
     * @var Repository
     */
    private $assetRepo;

    public function __construct(
        Repository $assetRepo,
        array $metaProviders
    ) {
        parent::__construct($metaProviders);
        $this->assetRepo = $assetRepo;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = ['fields' => ['children' => []]];
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $result['fields']['children'] = array_merge_recursive(
                $result['fields']['children'],
                $formGroup['metaClass']->getMeta($entityConfig, $formGroup['arguments'] ?? [])
            );
        }
        $this->modifyMeta($result);

        return $result;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        $result = [];
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $result = array_merge_recursive($result, $formGroup['metaClass']->getData($profileConfig));
        }
        if (empty($result)) {
            return [];
        }

        $result['fields']['catalog_product_entity']['use_custom_prefix'] = $profileConfig
            ->getExtensionAttributes()->getUseCustomPrefix();
        $result['fields']['catalog_product_entity']['field_postfix'] = $profileConfig
            ->getExtensionAttributes()->getFieldPostfix();

        return ['fields' => $result];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $params = $request->getParams();
        $fields = $params['fields'] ?? [];
        unset($params['fields']);
        $params = array_merge_recursive($params, $fields);
        $request->setParams($params);
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $formGroup['metaClass']->prepareConfig($profileConfig, $request);
        }

        $profileConfig->getExtensionAttributes()->setUseCustomPrefix(
            $params['fields']['catalog_product_entity']['use_custom_prefix'] ?? '0'
        );

        return $this;
    }

    protected function modifyMeta(array &$meta): void
    {
        $fieldPostfixMeta = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Entity Key Delimiter'),
                        'dataType' => 'text',
                        'default' => '.',
                        'visible' => true,
                        'formElement' => 'input',
                        'componentType' => 'field',
                        'sortOrder' => '5',
                        'tooltipTpl' => 'Amasty_ImportCore/form/element/tooltip',
                        'tooltip' => [
                            'description' => '<img src="'
                                . $this->assetRepo->getUrl(
                                    'Amasty_ProductImport::images/custom_prefix_tag_name.gif'
                                )
                                . '"/>'
                        ]
                    ]
                ]
            ]
        ];
        $useCustomPrefixMeta = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Custom Entity Key is Used'),
                        'dataType' => 'boolean',
                        'prefer' => 'toggle',
                        'valueMap' => ['true' => '1', 'false' => '0'],
                        'default' => '',
                        'sortOrder' => '0',
                        'formElement' => 'checkbox',
                        'visible' => true,
                        'componentType' => 'field',
                        'tooltipTpl' => 'Amasty_ImportCore/form/element/tooltip',
                        'tooltip' => [
                            'description' => '<img src="'
                                . $this->assetRepo->getUrl(
                                    'Amasty_ProductImport::images/custom_prefix_tag_name.gif'
                                )
                                . '"/>'
                        ]
                    ]
                ]
            ]
        ];
        $fields = &$meta['fields']['children']['fieldsConfigAdvanced']['children']
                   ['catalog_product_entity']['children'];
        $this->processPrefixFields($fields);

        $meta['fields']['children']['fieldsConfigAdvanced']['children']
        ['catalog_product_entity']['children']['field_postfix'] = $fieldPostfixMeta;
        $meta['fields']['children']['fieldsConfigAdvanced']['children']
        ['catalog_product_entity']['children']['use_custom_prefix'] = $useCustomPrefixMeta;
        $meta['fields']['children']['fieldsConfigAdvanced']['children']
        ['catalog_product_entity']['arguments']['data']['config'] = [
            'label' => __('Product'),
            'componentType' => 'fieldset',
            'visible' => true,
            'collapsible' => true,
            'opened' => true,
            'additionalClasses' => 'amproductimport-fieldset-withtooltip',
            'template' => 'Amasty_ProductImport/form/fieldset',
            'tooltipTpl' => 'Amasty_ImportCore/form/element/tooltip',
            'tooltip' => [
                'description' => '<img src="'
                    . $this->assetRepo->getUrl(
                        'Amasty_ProductImport::images/product_root_entity.gif'
                    )
                    . '"/>'
            ]
        ];

        $codeOutputField = &$meta['fields']['children']['fieldsConfigAdvanced']['children']
                            ['catalog_product_entity']['children']['field_code_input']['arguments']['data']['config'];
        $codeOutputField = array_merge($codeOutputField, [
            'sortOrder' => 1,
            'componentType' => 'field',
            'formElement' => 'input',
            'label' => __('Custom Entity Key'),
            'component' => 'Amasty_ProductImport/js/form/element/input',
            'tooltip' => [
                'description' => '<img src="'
                    . $this->assetRepo->getUrl(
                        'Amasty_ProductImport::images/custom_prefix_tag_name.gif'
                    )
                    . '"/>'
            ]
        ]);
        $meta['fields']['children']['fieldsConfigAdvanced']['children']
            ['catalog_product_entity']['children']['addField']['arguments']['data']['config']['sortOrder'] = 10;
    }

    private function processPrefixFields(array &$fields): void
    {
        foreach ($fields as $fieldName => &$field) {
            if (!isset($field['children'][$fieldName . '_container'])) {
                continue;
            }
            $field['children'][$fieldName . '_container']['children']
                ['field_code']['arguments']['data']['config']['formElement'] = 'hidden';
            $field['children'][$fieldName . '_container']['children']['field_code_input']
                ['arguments']['data']['config']['component'] = 'Amasty_ProductImport/js/form/element/custom-prefix';

            $this->processPrefixFields($field['children'][$fieldName . '_container']['children']);
        }
    }
}
