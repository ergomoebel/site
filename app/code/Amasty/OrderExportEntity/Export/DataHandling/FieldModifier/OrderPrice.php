<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */


declare(strict_types=1);

namespace Amasty\OrderExportEntity\Export\DataHandling\FieldModifier;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Magento\Directory\Model\Currency;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class OrderPrice extends AbstractModifier implements FieldModifierInterface
{
    const CURRENCY_IDENTIFIER = 'order_currency_code';

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        $config
    ) {
        parent::__construct($config);
        $this->priceCurrency = $priceCurrency;
    }

    public function transform($value)
    {
        return $this->priceCurrency->format(
            $value,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $this->currency
        );
    }

    public function prepareRowOptions(array $row)
    {
        if (isset($row[self::CURRENCY_IDENTIFIER]) && $this->isNeedLoadCurrency($row[self::CURRENCY_IDENTIFIER])) {
            $this->currency = $this->priceCurrency->getCurrency(null, $row[self::CURRENCY_IDENTIFIER]);
        }
    }

    private function isNeedLoadCurrency(string $currencyCode): bool
    {
        if ($this->currency instanceof Currency) {
            return $this->currency->getCurrencyCode() != $currencyCode;
        }

        return true;
    }

    public function getGroup(): string
    {
        return ModifierProvider::NUMERIC_GROUP;
    }

    public function getLabel(): string
    {
        return __('Price in Order Currency')->getText();
    }
}
