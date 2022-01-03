<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


namespace Amasty\OrderExport\Block\Adminhtml\Edit\Button;

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
            'label' => __('Save and Generate'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'order_export_profile_form.areas',
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
