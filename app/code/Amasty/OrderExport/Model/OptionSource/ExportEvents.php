<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ExportEvents implements OptionSourceInterface
{
    const ORDER_CREATION_ID = 1;
    const INVOICE_CREATION_ID = 2;
    const SHIPMENT_CREATION_ID = 3;
    const CREDIT_MEMO_CREATION_ID = 4;

    const ORDER_CREATION_EVENT = 'sales_order_place_after';
    const INVOICE_CREATION_EVENT = 'sales_order_invoice_save_after';
    const SHIPMENT_CREATION_EVENT = 'sales_order_shipment_save_after';
    const CREDIT_MEMO_CREATION_EVENT = 'sales_order_creditmemo_save_after';

    private $events = [
        self::ORDER_CREATION_EVENT => self::ORDER_CREATION_ID,
        self::INVOICE_CREATION_EVENT => self::INVOICE_CREATION_ID,
        self::SHIPMENT_CREATION_EVENT => self::SHIPMENT_CREATION_ID,
        self::CREDIT_MEMO_CREATION_EVENT => self::CREDIT_MEMO_CREATION_ID
    ];

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ORDER_CREATION_ID,
                'label'=> __('Order Creation (Event: %1)', self::ORDER_CREATION_EVENT)
            ],
            [
                'value' => self::INVOICE_CREATION_ID,
                'label'=> __('Invoice Creation (Event: %1)', self::INVOICE_CREATION_EVENT)
            ],
            [
                'value' => self::SHIPMENT_CREATION_ID,
                'label'=> __('Shipment Creation (Event: %1)', self::SHIPMENT_CREATION_EVENT)
            ],
            [
                'value' => self::CREDIT_MEMO_CREATION_ID,
                'label'=> __('Credit Memo Creation (Event: %1)', self::CREDIT_MEMO_CREATION_EVENT)
            ]
        ];

        return $options;
    }

    public function getEventIdByName($eventName)
    {
        return $this->events[strtolower($eventName)] ?? false;
    }
}
