<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */


namespace Amasty\OrderExportEntity\Export\Order\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Helper\Data;

class PaymentMethodOptions implements OptionSourceInterface
{
    /**
     * @var Data
     */
    private $paymentHelper;

    public function __construct(Data $paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
    }

    public function toOptionArray(): array
    {
        $result = [];
        if ($paymentMethods = $this->paymentHelper->getPaymentMethodList()) {
            foreach ($paymentMethods as $key => $label) {
                $result[] = ['value' => $key, 'label' => $label];
            }
        }

        return $result;
    }
}
