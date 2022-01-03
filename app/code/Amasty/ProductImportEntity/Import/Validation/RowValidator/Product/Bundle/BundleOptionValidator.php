<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Validation\RowValidator\Product\Bundle;

use Amasty\ImportCore\Api\Validation\RowValidatorInterface;
use Amasty\ProductImportEntity\Model\ResourceModel\CompositePKeyValidator;

class BundleOptionValidator implements RowValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    /**
     * @var CompositePKeyValidator
     */
    private $compositePKeyValidator;

    public function __construct(CompositePKeyValidator $compositePKeyValidator)
    {
        $this->compositePKeyValidator = $compositePKeyValidator;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $row): bool
    {
        $this->message = null;

        if (isset($row['option_id']) && isset($row['parent_id'])) {
            $isPKeyDuplicated = $this->compositePKeyValidator->isUniquePartDuplicated(
                $row,
                ['option_id', 'parent_id'],
                ['option_id'],
                'catalog_product_bundle_option'
            );
            if ($isPKeyDuplicated) {
                $this->message = (string)__(
                    'Bundle option \'option_id\' value %1 is already exists',
                    $row['option_id']
                );

                return false;
            }
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
