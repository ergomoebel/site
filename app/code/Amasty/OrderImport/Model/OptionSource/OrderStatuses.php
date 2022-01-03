<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Model\OptionSource;

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

    public function toOptionArray(): array
    {
        $values = [];

        foreach ($this->orderConfig->getStatuses() as $key => $status) {
            $values[] = [
                'value' => $key,
                'label' => $status
            ];
        }

        return $values;
    }
}
