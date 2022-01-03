<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImportEntity
 */

declare(strict_types=1);

namespace Amasty\CustomerImportEntity\Import\Validation\RowValidator;

use Amasty\ImportCore\Api\Validation\RowValidatorInterface;
use Amasty\ImportCore\Import\Utils\DuplicateFieldChecker;

class CustomerRowValidator implements RowValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    /**
     * @var DuplicateFieldChecker
     */
    private $duplicateFieldChecker;

    public function __construct(
        DuplicateFieldChecker $duplicateFieldChecker
    ) {
        $this->duplicateFieldChecker = $duplicateFieldChecker;
    }

    public function validate(array $row): bool
    {
        $this->message = null;

        if ($this->duplicateFieldChecker->hasDuplicateFields('customer_entity', $row)) {
            $this->message = __('A duplicate field was found in customer entity.')->render();

            return false;
        }

        return true;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
