<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


namespace Amasty\CronSchedule\Model\DataProvider\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class CronFrequency implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => '         ', 'label' => __('Custom')],
            ['value' => '* * * * *', 'label' => __('Every Minute')],
            ['value' => '0 * * * *', 'label' => __('Every Hour')],
            ['value' => '0 4 * * *', 'label' => __('Every Day at 4 am')],
            ['value' => '0 4 * * 1', 'label' => __('Every Monday at 4 am')],
            ['value' => '0 4 1 * *', 'label' => __('Every 1st Day of Month at 4am')],
        ];
    }
}
