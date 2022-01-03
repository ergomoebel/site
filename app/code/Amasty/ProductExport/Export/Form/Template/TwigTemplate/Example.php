<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */

declare(strict_types=1);

namespace Amasty\ProductExport\Export\Form\Template\TwigTemplate;

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
        return '{# Comment: You should add product `sku`'
            . "\n" . 'Enable Customer Review subentity and add `nickname` field. #}'
            . "\n" . '{'
            . "\n    " . '"caption": "Product {{ item.sku }}",'
            . "\n    " . '"reviews_count": {{ item.catalog_product_review|length }},'
            . "\n    " . '"who_left_review": [,'
            . "\n    " . '{% for review in item.catalog_product_review %}'
            . "\n        " . '"{{ review.nickname }}"{% if not loop.last %},{% endif %}'
            . "\n    " . '{% endfor %}'
            . "\n    " . ']'
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
