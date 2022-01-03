<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ExportEvents implements OptionSourceInterface
{
    const CUSTOMER_REGISTER_ID = 1;

    const CUSTOMER_REGISTER = 'customer_register_success';

    private $events = [
        self::CUSTOMER_REGISTER => self::CUSTOMER_REGISTER_ID
    ];

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CUSTOMER_REGISTER_ID,
                'label'=> __('Customer Registration (Event: %1)', self::CUSTOMER_REGISTER)
            ]
        ];
    }

    public function getEventIdByName($eventName)
    {
        return $this->events[strtolower($eventName)] ?? false;
    }
}
