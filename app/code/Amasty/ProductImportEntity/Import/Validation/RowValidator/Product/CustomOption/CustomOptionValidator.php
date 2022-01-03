<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Validation\RowValidator\Product\CustomOption;

use Amasty\ImportCore\Api\Validation\RowValidatorInterface;

class CustomOptionValidator implements RowValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    /**
     * @inheritDoc
     */
    public function validate(array $row): bool
    {
        $this->message = null;

        if (!isset($row['option_id'])) {
            $this->message = (string)__('\'option_id\' value is missing in Product Custom Option data.');

            return false;
        }

        if (!isset($row['product_id'])) {
            $this->message = (string)__('\'product_id\' value is missing in Product Custom Option data.');

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}
