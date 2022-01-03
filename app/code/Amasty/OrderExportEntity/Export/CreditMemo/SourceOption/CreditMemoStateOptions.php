<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */


namespace Amasty\OrderExportEntity\Export\CreditMemo\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\Order\Creditmemo;

class CreditMemoStateOptions implements OptionSourceInterface
{
    /**
     * @var Creditmemo
     */
    private $creditmemo;

    public function __construct(Creditmemo $creditmemo)
    {
        $this->creditmemo = $creditmemo;
    }

    public function toOptionArray(): array
    {
        $result = [];
        if ($currencies = $this->creditmemo->getStates()) {
            foreach ($currencies as $key => $label) {
                $result[] = ['value' => $key, 'label' => $label];
            }
        }

        return $result;
    }
}
