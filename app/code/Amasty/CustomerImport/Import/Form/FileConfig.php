<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


namespace Amasty\CustomerImport\Import\Form;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Amasty\ImportCore\Import\Form\CompositeForm;
use Amasty\ImportCore\Import\Source\Type\Csv\Reader as CsvReader;
use Amasty\ImportPro\Import\Source\Type\Ods\Reader as OdsReader;
use Amasty\ImportPro\Import\Source\Type\Xlsx\Reader as XlsxReader;
use Magento\Framework\App\RequestInterface;

class FileConfig extends CompositeForm implements FormInterface
{
    const DELIMITER_TARGET = 'index = field_postfix';

    const TEMPLATES_WITH_DELIMITER = [
        OdsReader::TYPE_ID,
        XlsxReader::TYPE_ID,
        CsvReader::TYPE_ID
    ];

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = ['file_config' => ['children' => []]];
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $result['file_config']['children'] = array_merge_recursive(
                $result['file_config']['children'],
                $formGroup['metaClass']->getMeta($entityConfig, $formGroup['arguments'] ?? [])
            );
        }
        $this->modifyMeta($result);

        return $result;
    }

    private function modifyMeta(array &$meta): void
    {
        if (!isset($meta['file_config']['children']['source_config']['children'])) {
            return;
        }

        foreach ($meta['file_config']['children']['source_config']['children'] as $type => &$source) {
            $type = str_replace('source_', '', $type);
            unset($source['children'][$type . '.postfix']);
        }
        $selectData = &$meta['file_config']['children']['source_config']['children']
        ['source']['arguments']['data']['config'];
        $selectData['switcherConfig']['enabled'] = true;

        foreach ($selectData['options'] as $index => $data) {
            $selectData['switcherConfig']['rules'][$index] = [
                'value' => $data['value'],
                'actions' => [
                    [
                        'target' => self::DELIMITER_TARGET,
                        'callback' => 'visible',
                        'params' => [in_array($data['value'], self::TEMPLATES_WITH_DELIMITER) ? true : false]
                    ]
                ]
            ];
        }
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

        $profileConfig->getExtensionAttributes()->setFieldPostfix(
            $result['extension_attributes'][$profileConfig->getSourceType() . '_source']['prefix'] ?? ''
        );

        return ['file_config' => $result];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $params = $request->getParams();

        $sourceType = $params['file_config']['source_type'] ?? null;
        $sourcePostfix = $params['fields']['fields']['customer_entity']['field_postfix'] ?? null;

        if ($sourcePostfix !== null && $sourceType !== null) {
            $params['file_config']['extension_attributes']
            [$sourceType . '_source']['postfix'] = $sourcePostfix;
        }

        $outputOptions = $params['file_config'] ?? [];
        unset($params['file_config']);
        $params = array_merge_recursive($params, $outputOptions);
        $request->setParams($params);

        foreach ($this->getFormGroupProviders() as $formGroup) {
            $formGroup['metaClass']->prepareConfig($profileConfig, $request);
        }

        return $this;
    }
}
