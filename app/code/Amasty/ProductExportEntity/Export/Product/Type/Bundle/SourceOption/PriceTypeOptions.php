<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\Product\Type\Bundle\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;

class PriceTypeOptions implements OptionSourceInterface
{
    const PRICE_TYPE_FIXED = 0;
    const PRICE_TYPE_PERCENT = 1;

    /**
     * @var array
     */
    private $options;

    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => self::PRICE_TYPE_FIXED,
                    'label' => __('fixed')
                ],
                [
                    'value' => self::PRICE_TYPE_PERCENT,
                    'label' => __('percent')
                ]
            ];
        }
        return $this->options;
    }
}
