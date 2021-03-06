<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */

declare(strict_types=1);

namespace Amasty\CustomerExport\Export\Form;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Amasty\ExportCore\Export\Form\CompositeForm;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset;

class Filters extends CompositeForm implements FormInterface
{
    /**
     * @var Asset\Repository
     */
    private $assetRepository;

    public function __construct(
        Asset\Repository $assetRepository,
        array $metaProviders
    ) {
        parent::__construct($metaProviders);
        $this->assetRepository = $assetRepository;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $result = ['conditions' => ['children' => []]];
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $result['conditions']['children'] = array_merge_recursive(
                $result['conditions']['children'],
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

        return ['conditions' => $result];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $params = $request->getParams();
        $conditions = $params['conditions'] ?? [];
        unset($params['conditions']);
        $params = array_merge_recursive($params, $conditions);
        $request->setParams($params);
        foreach ($this->getFormGroupProviders() as $formGroup) {
            $formGroup['metaClass']->prepareConfig($profileConfig, $request);
        }

        return $this;
    }
}
