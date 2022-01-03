<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Block\Adminhtml\Edit\Button;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(\Magento\Backend\Block\Widget\Context $context)
    {
        parent::__construct($context);
        $this->escaper = $context->getEscaper();
    }

    public function getButtonData()
    {
        if ($this->getProfileId() && !$this->isDuplicate()) {
            $alertMessage = __('Are you sure you want to delete?');
            $onClick = sprintf(
                'deleteConfirm("%s", "%s")',
                $this->escaper->escapeJs($alertMessage),
                $this->getDeleteUrl()
            );
            return [
                'label'          => __('Delete'),
                'class'          => 'delete',
                'id'             => 'profile-edit-delete-button',
                'on_click'       => $onClick,
                'sort_order'     => 20,
            ];
        }

        return [];
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getProfileId()]);
    }
}
