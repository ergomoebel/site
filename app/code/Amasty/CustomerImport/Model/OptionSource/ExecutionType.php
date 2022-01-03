<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


declare(strict_types=1);

namespace Amasty\CustomerImport\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ExecutionType implements OptionSourceInterface
{
    const MANUAL = 'manual';
    const CRON = 'cron';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::MANUAL, 'label'=> __('Manual')],
            ['value' => self::CRON, 'label'=> __('Cron')]
        ];
    }
}
