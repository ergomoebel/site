<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Export\Form;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

class Batch implements FormInterface
{
    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        return [
            'batch_size' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label'         => __('Batch Size'),
                            'visible'       => true,
                            'dataScope'     => 'batch_size',
                            'dataType'      => 'input',
                            'formElement'   => 'input',
                            'componentType' => 'input',
                            'component'     => 'Amasty_ExportPro/js/form/element/batchsize',
                            'sortOrder'     => 1,
                            'service'       => [
                                'template' => 'ui/form/element/helper/service',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        return ['batch_size' => $profileConfig->getBatchSize()];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $batchSize = $request->getParam('batch_size');
        $useDefault = $request->getParam('use_default');
        if (!empty($useDefault['batch_size'])) {
            $profileConfig->setBatchSize(null);
        } elseif (!empty($batchSize)) {
            $profileConfig->setBatchSize($batchSize);
        }

        return $this;
    }
}
