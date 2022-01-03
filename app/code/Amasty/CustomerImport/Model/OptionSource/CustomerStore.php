<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */

declare(strict_types=1);

namespace Amasty\CustomerImport\Model\OptionSource;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

class CustomerStore extends StoreOptions
{
    /**
     * No selection value
     */
    const NO_SELECTION = 0;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->currentOptions['No Selection'] = [
            'label' => __('-- Please Select --'),
            'value' => self::NO_SELECTION
        ];

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
