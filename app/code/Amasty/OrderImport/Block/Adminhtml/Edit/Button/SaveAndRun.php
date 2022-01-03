<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */


namespace Amasty\OrderImport\Block\Adminhtml\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndRun extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->getProfileId() || $this->isDuplicate()) {
            return [];
        }
        return [
            'label' => __('Save & Import'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'order_import_profile_form.areas',
                                'actionName' => 'saveAndRun'
                            ]
                        ]
                    ]
                ],
            ],
            'on_click' => '',
            'sort_order' => 10
        ];
    }
}
