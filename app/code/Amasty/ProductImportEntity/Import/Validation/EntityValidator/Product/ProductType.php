<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Validation\EntityValidator\Product;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;

class ProductType implements FieldValidatorInterface
{
    /**
     * @var ConfigInterface
     */
    private $typeConfig;

    public function __construct(ConfigInterface $typeConfig)
    {
        $this->typeConfig = $typeConfig;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $value = trim($row[$field]);
            $types = $this->typeConfig->getType($value);

            return !empty($types);
        }

        return true;
    }
}
