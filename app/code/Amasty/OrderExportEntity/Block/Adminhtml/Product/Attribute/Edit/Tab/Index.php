<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Amasty\OrderExportEntity\Model\AttributeFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Eav\Helper\Data;
use Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;

class Index extends AbstractMain implements TabInterface
{
    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $eavData,
        YesnoFactory $yesnoFactory,
        InputtypeFactory $inputTypeFactory,
        PropertyLocker $propertyLocker,
        AttributeFactory $attributeFactory,
        array $data = []
    ) {
        $this->attributeFactory = $attributeFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $eavData,
            $yesnoFactory,
            $inputTypeFactory,
            $propertyLocker,
            $data
        );
    }

    public function getTabLabel(): Phrase
    {
        return __('Order Export');
    }

    public function getTabTitle(): Phrase
    {
        return __('Order Export');
    }

    public function canShowTab(): bool
    {
        return true;
    }

    public function isHidden(): bool
    {
        return false;
    }

    /**
     * @return $this|Index
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $catalogAttributeObject = $this->getAttributeObject();
        $attributeObject = $this->attributeFactory->create()->load($catalogAttributeObject->getId(), 'attribute_id');
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $fieldset = $form->addFieldset(
            'amasty_order_export_attribute_index_fieldset',
            ['legend' => __('Order Export'), 'collapsable' => true]
        );
        $yesno = $this->_yesnoFactory->create()->toOptionArray();
        $fieldset->addField(
            'amasty_order_export_attribute_use_in_index',
            'select',
            [
                'name' => 'amasty_order_export_attribute_use_in_index',
                'label' => __('Add to Options'),
                'title' => __('Add to Options'),
                'note' => __('Select "Yes" to add this attribute to the list of options in the order export.'),
                'values' => $yesno,
                'value' => $attributeObject->getId() ? 1 : 0
            ]
        );
        $this->setForm($form);

        return $this;
    }

    public function getAfter(): string
    {
        return 'front';
    }
}
