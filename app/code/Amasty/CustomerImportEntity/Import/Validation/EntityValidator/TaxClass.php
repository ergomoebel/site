<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImportEntity
 */


declare(strict_types=1);

namespace Amasty\CustomerImportEntity\Import\Validation\EntityValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Api\TaxClassRepositoryInterface;

class TaxClass implements FieldValidatorInterface
{
    /**
     * @var TaxClassRepositoryInterface
     */
    private $taxRepository;

    /**
     * @var array
     */
    private $validationResult = [];

    public function __construct(TaxClassRepositoryInterface $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $taxClassId = trim($row[$field]);

            if (!empty($taxClassId)) {
                if (!isset($this->validationResult[$taxClassId])) {
                    $this->validationResult[$taxClassId] = $this->isTaxClassExists($taxClassId);
                }

                return $this->validationResult[$taxClassId];
            }
        }

        return true;
    }

    private function isTaxClassExists($taxClassId): bool
    {
        try {
            $this->taxRepository->get($taxClassId);
            $this->validationResult[trim($taxClassId)] = true;
        } catch (NoSuchEntityException $e) {
            $this->validationResult[trim($taxClassId)] = false;
        }

        return $this->validationResult[trim($taxClassId)];
    }
}
