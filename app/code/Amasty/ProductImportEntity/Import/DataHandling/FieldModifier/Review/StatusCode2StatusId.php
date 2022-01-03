<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\FieldModifier\Review;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;

use Magento\Review\Helper\Data as StatusSource;

class StatusCode2StatusId implements FieldModifierInterface
{
    /**
     * @var StatusSource
     */
    private $source;

    /**
     * @var array|null
     */
    private $map;

    public function __construct(StatusSource $source)
    {
        $this->source = $source;
    }

    public function transform($value)
    {
        if (!is_array($value)) {
            $map = $this->getMap();
            return $map[$value] ?? $value;
        }

        return $value;
    }

    private function getMap(): ?array
    {
        if (!$this->map) {
            $statuses = array_map(function ($status) {
                return $status->getText();
            }, $this->source->getReviewStatuses());
            $this->map = array_flip($statuses);
        }
        return $this->map;
    }
}
