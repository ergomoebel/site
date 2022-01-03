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

class ExportNewEntities implements FormInterface
{
    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        return [
            'export_new_entities' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => (!empty($arguments['label'])
                                ? __($arguments['label'])
                                : __('Export Only New Entities')),
                            'tooltip'       => [
                                'description' => (!empty($arguments['description'])
                                    ? __($arguments['description'])
                                    : __('Only newly created entities (Order, Invoice, Shipment…) will be '
                                        . 'exported if the setting is enabled. The entities exported previously '
                                        . 'will be skipped.'))
                            ],
                            'dataType'      => 'boolean',
                            'formElement'   => 'checkbox',
                            'default'       => 0,
                            'prefer' => 'toggle',
                            'visible'       => true,
                            'componentType' => 'field',
                            'sortOrder'     => 1,
                            'valueMap' => ['true' => 1, 'false' => 0],
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        return ['export_new_entities' => $profileConfig->getExtensionAttributes()->getExportNewEntities() ? 1 : 0];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $profileConfig->getExtensionAttributes()->setExportNewEntities(
            $request->getParam('export_new_entities')
        );

        return $this;
    }
}
