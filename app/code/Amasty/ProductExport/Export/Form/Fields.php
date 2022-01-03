<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */

declare(strict_types=1);

namespace Amasty\ProductExport\Export\Form;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Amasty\ExportCore\Export\Form\CompositeForm;
use Amasty\ExportCore\Export\Utils\Hash;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository;

class Fields extends CompositeForm implements FormInterface
{
    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var Hash
     */
    private $hash;

    public function __construct(
        Repository $assetRepo,
        Hash $hash,
        array $metaProviders
    ) {
        parent::__construct($metaProviders);
        $this->assetRepo = $assetRepo;
        $this->hash = $hash;
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
            $params["fields"]["catalog_product_entity"]["use_custom_prefix"] ?? '0'
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
                        'tooltipTpl' => 'Amasty_ExportCore/form/element/tooltip',
                        'tooltip' => [
                            'description' => '<img src="'
                                . $this->assetRepo->getUrl(
                                    'Amasty_ProductExport::images/custom_prefix_tag_name.gif'
                                )
                                . '"/>'
                        ]
                    ]
                ]
            ]
        ];
        $meta["fields"]["children"]["fieldsConfigAdvanced"]["children"]
        [$this->hash->hash('catalog_product_entity')]["children"]['field_postfix'] = $fieldPostfixMeta;
        $meta['fields']['children']['fieldsConfigAdvanced']['children']
        [$this->hash->hash('catalog_product_entity')]['children']
        ['addField']['arguments']['data']['config']['sortOrder'] = 10;

        $fieldCodeOuputConfig = &$meta['fields']['children']['fieldsConfigAdvanced']['children']
        [$this->hash->hash('catalog_product_entity')]['children']['field_code_output']['arguments']['data']['config'];
        $fieldCodeOuputConfig['tooltip'] = [
            'description' => '<img src="'
                . $this->assetRepo->getUrl(
                    'Amasty_ProductExport::images/custom_prefix_tag_name.gif'
                )
                . '"/>'
        ];

        $productEntityConfig = &$meta['fields']['children']['fieldsConfigAdvanced']
           ['children'][$this->hash->hash('catalog_product_entity')]['arguments']['data']['config'];
        $productEntityConfig['label'] = __('Product (root entity)');
        $productEntityConfig['additionalClasses'] = 'amproductexport-fieldset-withtooltip';
        $productEntityConfig['template'] = 'Amasty_ProductExport/form/fieldset';
        $productEntityConfig['tooltipTpl'] = 'Amasty_ExportCore/form/element/tooltip';
        $productEntityConfig['tooltip'] = [
            'description' => '<img src="'
                . $this->assetRepo->getUrl(
                    'Amasty_ProductExport::images/product_root_entity.gif'
                )
                . '"/>'
        ];
    }
}
