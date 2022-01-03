<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Export\Form\Template\TwigTemplate\Json;

use Amasty\ExportPro\Export\Template\Type\Twig\TwigTemplateInterface;

class Example implements TwigTemplateInterface
{
    public function getName(): string
    {
        return (string)__('Load JSON Sample');
    }

    public function getHeader(): string
    {
        return '[';
    }

    public function getContent(): string
    {
        return '{# Comment: You should add order `increment_id`, `grand_total`,'
            . '`order_currency_code` fields in Fields Configuration tab.'
            . "\n" . 'Enable Order Item subentity and add `is_virtual` field. #}'
            . "\n" . '{'
            . "\n    " . '"caption": "Order #{{ item.increment_id }} costs {{ item.grand_total }}'
            . ' {{ item.order_currency_code }}",'
            . "\n    " . '"items_count": {{ item.sales_order_item|length }},'
            . "\n    " . "{% set virtual_count = 0 %}"
            . "\n    " . '{% for order_item in item.sales_order_item %}'
            . "\n        " . '{% if order_item.is_virtual %}'
            . "\n            " . '{% set virtual_count = virtual_count + 1  %}'
            . "\n        " . '{% endif %}'
            . "\n    " . '{% endfor %}'
            . "\n    " . '"virtual_items_count": {{ virtual_count }}'
            . "\n}";
    }

    public function getSeparator(): string
    {
        return ',';
    }

    public function getFooter(): string
    {
        return ']';
    }

    public function getExtension(): string
    {
        return 'json';
    }
}
