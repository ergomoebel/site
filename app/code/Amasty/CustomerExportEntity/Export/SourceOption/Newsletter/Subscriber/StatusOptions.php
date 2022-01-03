<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExportEntity
 */

declare(strict_types=1);

namespace Amasty\CustomerExportEntity\Export\SourceOption\Newsletter\Subscriber;

use Magento\Framework\Data\OptionSourceInterface;

class StatusOptions implements OptionSourceInterface
{
    const STATUS_SUBSCRIBED = 1;
    const STATUS_NOT_ACTIVE = 2;
    const STATUS_UNSUBSCRIBED = 3;
    const STATUS_UNCONFIRMED = 4;

    public function toOptionArray(): array
    {
        $optionArray = [];
        foreach ($this->toArray() as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::STATUS_SUBSCRIBED => __('Subscribed'),
            self::STATUS_NOT_ACTIVE => __('Not Activated'),
            self::STATUS_UNSUBSCRIBED => __('Unsubscribed'),
            self::STATUS_UNCONFIRMED => __('Unconfirmed')
        ];
    }
}
