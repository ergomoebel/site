<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Form;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Amasty\ExportCore\Export\Form\CompositeForm;
use Magento\Framework\App\RequestInterface;

class AlertNotifications extends CompositeForm implements FormInterface
{
    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = ['alert_notifications' => ['children' => []]];
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $result['alert_notifications']['children'] = array_merge_recursive(
                $result['alert_notifications']['children'],
                $formGroup['metaClass']->getMeta($entityConfig, $formGroup['arguments'] ?? [])
            );
        }

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

        return ['alert_notifications' => $result];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $params = $request->getParams();
        $outputOptions = $params['alert_notifications'] ?? [];
        unset($params['alert_notifications']);
        $params = array_merge_recursive($params, $outputOptions);
        $request->setParams($params);
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $formGroup['metaClass']->prepareConfig($profileConfig, $request);
        }

        return $this;
    }
}
