<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Validation\EntityValidator\Product;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class Sku implements FieldValidatorInterface
{
    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var array
     */
    private $validationResult;

    public function __construct(ProductResource $productResource)
    {
        $this->productResource = $productResource;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $sku = trim($row[$field]);

            if (!empty($sku)) {
                if (!isset($this->validationResult[$sku])) {
                    $this->validationResult[$sku] = $this->productResource->getIdBySku($sku) !== false;
                }

                return $this->validationResult[$sku];
            }
        }

        return true;
    }
}
