<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteImportEntity
 */


declare(strict_types=1);

namespace Amasty\UrlRewriteImportEntity\Import\Validation\RowValidator;

use Amasty\ImportCore\Api\Validation\RowValidatorInterface;
use Amasty\ImportCore\Import\Utils\DuplicateFieldChecker;

class UrlRewrite implements RowValidatorInterface
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

        if ($this->duplicateFieldChecker->hasDuplicateFields('url_rewrite', $row)) {
            $this->message = __('A duplicate field was found in URL rewrite entity.')->render();

            return false;
        }

        return true;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
