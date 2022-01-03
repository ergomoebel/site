<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderImportEntity\Import\Validation\EntityValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Payment\Helper\Data;

class PaymentMethod implements FieldValidatorInterface
{
    /**
     * @var Data
     */
    private $paymentHelper;

    /**
     * @var array
     */
    private $validationResult;

    public function __construct(
        Data $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $method = trim($row[$field]);

            if (!empty($country)) {
                if (!isset($this->validationResult[$method])) {
                    $allMethods = $this->paymentHelper->getPaymentMethods();
                    $this->validationResult[$method] = isset($allMethods[trim($method)]);
                }

                return $this->validationResult[$method];
            }
        }

        return true;
    }
}
