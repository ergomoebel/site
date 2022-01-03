<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */


namespace Amasty\OrderExportEntity\Export\Order\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\Order\Config;

class OrderState implements OptionSourceInterface
{
    /**
     * @var Config
     */
    private $orderConfig;

    public function __construct(Config $orderConfig)
    {
        $this->orderConfig = $orderConfig;
    }

    public function toOptionArray(): array
    {
        $result = [];
        if ($data = $this->orderConfig->getStates()) {
            foreach ($data as $value => $label) {
                $result[] = ['value' => $value, 'label' => $label];
            }
        }

        return $result;
    }
}
