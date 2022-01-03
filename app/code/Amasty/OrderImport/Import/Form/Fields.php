<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */


namespace Amasty\OrderImport\Import\Form;

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

        $result['fields']['sales_order']['use_custom_prefix'] = $profileConfig
            ->getExtensionAttributes()->getUseCustomPrefix();
        $result['fields']['sales_order']['field_postfix'] = $profileConfig
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
            $params['fields']['sales_order']['use_custom_prefix'] ?? '0'
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
                                    'Amasty_ImportCore::images/custom_prefix_tag_name.gif'
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
                                    'Amasty_ImportCore::images/custom_prefix_tag_name.gif'
                                )
                                . '"/>'
                        ]
                    ]
                ]
            ]
        ];
        $fields = &$meta['fields']['children']['fieldsConfigAdvanced']['children']['sales_order']['children'];
        $this->processPrefixFields($fields);

        $meta['fields']['children']['fieldsConfigAdvanced']['children']
        ['sales_order']['children']['field_postfix'] = $fieldPostfixMeta;
        $meta['fields']['children']['fieldsConfigAdvanced']['children']
        ['sales_order']['children']['use_custom_prefix'] = $useCustomPrefixMeta;
        $meta['fields']['children']['fieldsConfigAdvanced']['children']
        ['sales_order']['arguments']['data']['config'] = [
            'label' => __('Order'),
            'componentType' => 'fieldset',
            'visible' => true,
            'collapsible' => true,
            'opened' => true,
            'additionalClasses' => 'amorderimport-fieldset-withtooltip',
            'template' => 'Amasty_OrderImport/form/fieldset',
            'tooltipTpl' => 'Amasty_ImportCore/form/element/tooltip',
            'tooltip' => [
                'description' => '<img src="'
                    . $this->assetRepo->getUrl(
                        'Amasty_OrderImport::images/order_root_entity.gif'
                    )
                    . '"/>'
            ]
        ];

        $codeOutputField = &$meta['fields']['children']['fieldsConfigAdvanced']['children']
                            ['sales_order']['children']['field_code_input']['arguments']['data']['config'];
        $codeOutputField = array_merge($codeOutputField, [
            'sortOrder' => 1,
            'componentType' => 'field',
            'formElement' => 'input',
            'label' => __('Custom Entity Key'),
            'component' => 'Amasty_OrderImport/js/form/element/input'
        ]);
        $meta['fields']['children']['fieldsConfigAdvanced']['children']
            ['sales_order']['children']['addField']['arguments']['data']['config']['sortOrder'] = 10;
    }

    private function processPrefixFields(array &$fields): void
    {
        foreach ($fields as $fieldName => &$field) {
            if (!isset($field['children'][$fieldName . '_container'])) {
                continue;
            }
            $field['children'][$fieldName . '_container']['children']['field_code']
                ['arguments']['data']['config']['formElement'] = 'hidden';
            $field['children'][$fieldName . '_container']['children']['field_code_input']
                ['arguments']['data']['config']['component'] = 'Amasty_OrderImport/js/form/element/custom-prefix';
            $this->processPrefixFields($field['children'][$fieldName . '_container']['children']);
        }
    }
}
