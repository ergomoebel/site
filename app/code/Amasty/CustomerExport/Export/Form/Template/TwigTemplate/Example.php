<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */

declare(strict_types=1);

namespace Amasty\CustomerExport\Export\Form\Template\TwigTemplate;

use Amasty\ExportPro\Export\Template\Type\Twig\TwigTemplateInterface;

class Example implements TwigTemplateInterface
{
    public function getName(): string
    {
        return (string)__('Example Template');
    }

    public function getHeader(): string
    {
        return '[';
    }

    public function getContent(): string
    {
        return '{# Comment: You should add customer `firstname`, `lastname` fields in Fields Configuration tab.'
            . "\n" . 'Enable Customer Group subentity and add `customer_group_code` field.'
            . "\n" . 'Enable Customer Address subentity and add `postcode` field #}'
            . "\n" . '{'
            . "\n    " . '"greetings": "Hello {{ item.firstname }} {{ item.lastname }}",'
            . "\n    " . '"user_created_day": "{{ item.created_at|date(\'l\') }}",'
            . "\n" . '{# Extract customer group to variable #}'
            . "\n    " . "{% set customer_group = item.customer_group|last %}"
            . "\n    " . '"group": "Customer is in `{{ customer_group.customer_group_code }}` group",'
            . "\n    " . '"postcodes" : ['
            . "\n        " . '{% for address in item.customer_address_entity %}'
            . "\n        " . '"{{address.postcode}}"'
            . "\n        " . '{% endfor %}'
            . "\n    ]"
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
