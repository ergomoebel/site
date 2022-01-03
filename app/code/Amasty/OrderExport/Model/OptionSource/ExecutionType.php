<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ExecutionType implements OptionSourceInterface
{
    const MANUAL = 'manual';
    const CRON = 'cron';
    const EVENT = 'event';
    const EVENT_AND_CRON = 'event_cron';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::MANUAL, 'label'=> __('Manual')],
            ['value' => self::CRON, 'label'=> __('Cron')],
            ['value' => self::EVENT, 'label'=> __('Event')],
            ['value' => self::EVENT_AND_CRON, 'label' => __('Event and Cron')]
        ];
    }
}
