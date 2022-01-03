<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


declare(strict_types=1);

namespace Amasty\ProductExport\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ExportEvents implements OptionSourceInterface
{
    const PRODUCT_AFTER_SAVE_ID = 1;

    const PRODUCT_AFTER_SAVE_EVENT = 'catalog_product_save_after';

    private $events = [
        self::PRODUCT_AFTER_SAVE_EVENT => self::PRODUCT_AFTER_SAVE_ID,
    ];

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::PRODUCT_AFTER_SAVE_ID,
                'label'=> __('Product Save (Event: %1)', self::PRODUCT_AFTER_SAVE_EVENT)
            ]
        ];

        return $options;
    }

    public function getEventIdByName($eventName)
    {
        return $this->events[strtolower($eventName)] ?? false;
    }
}
