<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\Product\Type\Bundle\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;

class TypeOptions implements OptionSourceInterface
{
    const TYPE_DYNAMIC = '0';

    const TYPE_FIXED = '1';

    /**
     * @var array
     */
    private $options;

    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => self::TYPE_DYNAMIC,
                    'label' => __('dynamic')
                ],
                [
                    'value' => self::TYPE_FIXED,
                    'label' => __('fixed')
                ]
            ];
        }
        return $this->options;
    }
}
