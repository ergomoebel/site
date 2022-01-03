<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */

declare(strict_types=1);

namespace Amasty\ProductImport\Import\Action\PostImport\Product;

use Amasty\ProductImport\Api\ProductActionInterface;
use Amasty\ProductImport\Import\Action\Import\Product\ProductActions as BaseProductActions;

class ProductActions extends BaseProductActions
{
    /**
     * @inheritdoc
     */
    protected $actionGroup = ProductActionInterface::GROUP_FULL_SET;
}
