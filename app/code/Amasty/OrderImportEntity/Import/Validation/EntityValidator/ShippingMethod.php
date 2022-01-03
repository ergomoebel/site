<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */


declare(strict_types=1);

namespace Amasty\OrderImportEntity\Import\Validation\EntityValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Shipping\Model\Config\Source\Allmethods;

class ShippingMethod implements FieldValidatorInterface
{
    /**
     * @var Allmethods
     */
    private $allMethods;

    /**
     * @var array
     */
    private $validationResult = [];

    public function __construct(Allmethods $allMethods)
    {
        $this->allMethods = $allMethods;
    }

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $shipping = trim($row[$field]);

            if (!empty($shipping)) {
                if (!isset($this->validationResult[$shipping])) {
                    $this->validationResult[$shipping] = $this->isShippingMethodExists($shipping);
                }

                return $this->validationResult[$shipping];
            }
        }

        return true;
    }

    private function isShippingMethodExists($shipping): bool
    {
        if ($shipping === '') { //virtual order
            $this->validationResult[trim($shipping)] = true;

            return $this->validationResult[trim($shipping)];
        }
        foreach ($this->allMethods->toOptionArray() as $shippingMethod) {
            if (!empty($shippingMethod['value']) && is_array($shippingMethod['value'])) {
                foreach ($shippingMethod['value'] as $valueLabelData) {
                    if (!empty($valueLabelData['value']) && $valueLabelData['value'] == trim($shipping)) {
                        $this->validationResult[trim($shipping)] = true;

                        return $this->validationResult[trim($shipping)];
                    }
                }
            }
        }

        $this->validationResult[trim($shipping)] = false;

        return $this->validationResult[trim($shipping)];
    }
}
