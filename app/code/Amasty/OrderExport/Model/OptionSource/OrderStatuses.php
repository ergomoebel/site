<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\Order\Config;

class OrderStatuses implements OptionSourceInterface
{
    /**
     * @var Config
     */
    private $orderConfig;

    public function __construct(
        Config $orderConfig
    ) {
        $this->orderConfig = $orderConfig;
    }

    public function toOptionArray()
    {
        $values = [['value' => '', 'label' => __('-- Please Select --')]];

        foreach ($this->orderConfig->getStatuses() as $key => $status) {
            $values[] = [
                'value' => $key,
                'label' => $status
            ];
        }

        return $values;
    }
}
