<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteImportEntity
 */


declare(strict_types=1);

namespace Amasty\UrlRewriteImportEntity\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\UrlRewriteImportEntity\Import\DataHandling\Entity\Category\NamesPath2EntityId;

class CatNamesPath2CatId implements FieldModifierInterface
{
    /**
     * @var NamesPath2EntityId
     */
    private $namesPath2EntityId;

    public function __construct(NamesPath2EntityId $namesPath2EntityId)
    {
        $this->namesPath2EntityId = $namesPath2EntityId;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (!empty($value)) {
            return $this->namesPath2EntityId->execute($value);
        }

        return $value;
    }
}
