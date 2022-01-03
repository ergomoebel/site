<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExportEntity
 */


declare(strict_types=1);

namespace Amasty\CustomerExportEntity\Export\SourceOption;

use Magento\Store\Ui\Component\Listing\Column\Store\Options;

class CreatedInOptions extends Options
{
    public function toOptionArray(): array
    {
        $this->generateCurrentOptions();
        foreach ($this->currentOptions as &$option) {
            $this->prepareOption($option);
        }

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }

    private function prepareOption(&$option): void
    {
        if (!empty($option['value']) && is_array($option['value'])) {
            foreach ($option['value'] as &$storeOption) {
                if (!empty($storeOption['value']) && is_array($storeOption['value'])) {
                    foreach ($storeOption['value'] as &$storeViewOption) {
                        if (isset($storeViewOption['value'], $storeViewOption['label'])) {
                            // 'created_in' value stored as label in database
                            $storeViewOption['value'] = trim($storeViewOption['label']);
                        }
                    }
                }
            }
        }
    }
}
