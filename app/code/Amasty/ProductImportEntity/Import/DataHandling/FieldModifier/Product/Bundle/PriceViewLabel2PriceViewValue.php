<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\FieldModifier\Product\Bundle;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Magento\Bundle\Model\Product\Attribute\Source\Price\View;

class PriceViewLabel2PriceViewValue implements FieldModifierInterface
{
    /**
     * @var View
     */
    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (!is_array($value) && !empty($value)) {
            return $this->view->getOptionId($value) ?? $value;
        }

        return $value;
    }
}
