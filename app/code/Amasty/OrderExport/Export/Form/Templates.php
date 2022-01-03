<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Export\Form;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Amasty\ExportCore\Export\Form\CompositeForm;
use Magento\Framework\App\RequestInterface;

class Templates extends CompositeForm implements FormInterface
{
    const DELIMITER_TARGET = "index = field_postfix";
    const TEMPLATES_WITH_DELIMITER = [
        \Amasty\ExportPro\Export\Template\Type\Ods\Renderer::TYPE_ID,
        \Amasty\ExportPro\Export\Template\Type\Xlsx\Renderer::TYPE_ID,
        \Amasty\ExportCore\Export\Template\Type\Csv\Renderer::TYPE_ID
    ];

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = ['templates' => ['children' => []]];
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $result['templates']['children'] = array_merge_recursive(
                $result['templates']['children'],
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
        $profileConfig->getExtensionAttributes()->setFieldPostfix(
            $result['extension_attributes'][$profileConfig->getTemplateType() . '_template']['postfix'] ?? ''
        );

        return ['templates' => $result];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $params = $request->getParams();
        $templateType = $params["templates"]["template_type"] ?? null;
        $templatePostfix = $params["fields"]["fields"]["sales_order"]["field_postfix"] ?? null;

        if ($templatePostfix !== null && $templateType !== null) {
            $params["templates"]["extension_attributes"]
            [$templateType . "_template"]['postfix'] = $templatePostfix;
        }
        $templates = $params['templates'] ?? [];
        unset($params['templates']);
        $params = array_merge_recursive($params, $templates);
        $request->setParams($params);
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $formGroup['metaClass']->prepareConfig($profileConfig, $request);
        }

        return $this;
    }

    protected function modifyMeta(array &$meta): void
    {
        if (!isset($meta["templates"]["children"]["template_config"]["children"])) {
            return;
        }

        foreach ($meta["templates"]["children"]["template_config"]["children"] as $type => &$template) {
            $type = str_replace('template_', '', $type);
            unset($template['children'][$type . '.postfix']);
        }
        $selectData = &$meta["templates"]["children"]["template_config"]["children"]
        ["template"]["arguments"]["data"]["config"];
        $selectData['switcherConfig']['enabled'] = true;

        foreach ($selectData["options"] as $index => $data) {
            $selectData['switcherConfig']['rules'][$index] = [
                'value' => $data['value'],
                'actions' => [
                     [
                         'target' => self::DELIMITER_TARGET,
                         'callback' => "visible",
                         'params' => [in_array($data['value'], self::TEMPLATES_WITH_DELIMITER) ? true : false]
                     ]
                ]
            ];
        }
    }
}
