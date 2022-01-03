<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */


namespace Amasty\OrderExportEntity\Export\Invoice\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\Order\Invoice;

class InvoiceStateOptions implements OptionSourceInterface
{
    /**
     * @var Invoice
     */
    private $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function toOptionArray(): array
    {
        $result = [];
        if ($currencies = $this->invoice->getStates()) {
            foreach ($currencies as $key => $label) {
                $result[] = ['value' => $key, 'label' => $label];
            }
        }

        return $result;
    }
}
