<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */

declare(strict_types=1);

namespace Amasty\ProductImport\Import\Action\Import\Product\ProductActions;

use Amasty\ProductImport\Api\ProductActionInterface;

abstract class AbstractAction implements ProductActionInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @inheritdoc
     */
    public function setOption(string $name, $value): ProductActionInterface
    {
        $this->options[$name] = $value;

        return $this;
    }
}
