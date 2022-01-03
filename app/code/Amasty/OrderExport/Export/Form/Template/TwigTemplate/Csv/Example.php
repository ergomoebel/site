<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Export\Form\Template\TwigTemplate\Csv;

use Amasty\ExportPro\Export\Template\Type\Twig\TwigTemplateInterface;

class Example implements TwigTemplateInterface
{
    public function getName(): string
    {
        return (string)__('Load CSV Sample');
    }

    public function getHeader(): string
    {
        return 'increment_id,grand_total,item.sku' . "\r\n";
    }

    public function getContent(): string
    {
        return '{# Comment: You should add order `increment_id`, `grand_total`,'
            . '`order_currency_code` fields in Fields Configuration tab.'
            . "\n" . 'Enable Order Item subentity and add `sku` field. #}'
            . "\n" . '{% set order_items = [] %}'
            . "\n" . '{% for order_item in item.sales_order_item %}'
            . "\n" . '{% set order_items = order_items|merge([order_item.sku]) %}'
            . "\n" . '{% endfor %}'
            . "\n" . '{% set order_items_ouput = order_items|join(\',\') %}'
            . "\n" . '#{{'
            . "\n" . '"#{ item.increment_id }"'
            . "\n" . '}},"{{'
            . "\n" . '"#{ item.grand_total } #{ item.order_currency_code }"'
            . "\n" . '}}","{{'
            . "\n" . '"#{ order_items_ouput }"'
            . "\n" . '}}"' . "\r\n";
    }

    public function getSeparator(): string
    {
        return '';
    }

    public function getFooter(): string
    {
        return '';
    }

    public function getExtension(): string
    {
        return 'csv';
    }
}
