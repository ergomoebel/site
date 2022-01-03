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
use Magento\Framework\Exception\LocalizedException;

class FileResolver extends CompositeForm implements FormInterface
{
    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = ['file_source' => ['children' => []]];
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $result['file_source']['children'] = array_merge_recursive(
                $result['file_source']['children'],
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

        return ['file_source' => $result];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $params = $request->getParams();
        $outputOptions = $params['file_source'] ?? [];
        unset($params['file_source']);
        $params = array_merge_recursive($params, $outputOptions);
        $request->setParams($params);
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $formGroup['metaClass']->prepareConfig($profileConfig, $request);
        }

        return $this;
    }
}
