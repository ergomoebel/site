<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


namespace Amasty\OrderExport\Export\Form;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Amasty\ExportCore\Export\Form\CompositeForm;
use Amasty\OrderExport\Model\ConfigProvider;
use Magento\Framework\App\RequestInterface;

class General extends CompositeForm implements FormInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(array $metaProviders, ConfigProvider $configProvider)
    {
        parent::__construct($metaProviders);
        $this->configProvider = $configProvider;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = ['general' => ['children' => ['configuration' => ['children' => []]]]];
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $result['general']['children']['configuration']['children'] = array_merge_recursive(
                $result['general']['children']['configuration']['children'],
                $formGroup['metaClass']->getMeta($entityConfig, $formGroup['arguments'] ?? [])
            );
        }
        $result['general']['children']['configuration']['children']['batch_size']
        ['arguments']['data']['config']['default'] = $this->configProvider->getBatchSize();

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

        return ['general' => $result];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $params = $request->getParams();
        $general = $params['general'] ?? [];
        $params = array_merge_recursive($params, $general);
        $request->setParams($params);
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $formGroup['metaClass']->prepareConfig($profileConfig, $request);
        }

        return $this;
    }
}
