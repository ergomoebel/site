<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */


namespace Amasty\OrderExportEntity\Export\Order\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Directory\Model\Currency;

class CurrencyOptions implements OptionSourceInterface
{
    /**
     * @var Currency
     */
    private $currency;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    public function toOptionArray(): array
    {
        $result = [];
        if ($currencies = $this->currency->getConfigAllowCurrencies()) {
            foreach ($currencies as $key => $currency) {
                $result[] = ['value' => $currency, 'label' => $currency];
            }
        }

        return $result;
    }
}
