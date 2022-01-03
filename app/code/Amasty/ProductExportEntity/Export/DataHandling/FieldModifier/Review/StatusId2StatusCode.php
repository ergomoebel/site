<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\DataHandling\FieldModifier\Review;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Magento\Review\Helper\Data as StatusSource;

class StatusId2StatusCode extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var StatusSource
     */
    private $source;

    /**
     * @var array|null
     */
    private $map;

    public function __construct(StatusSource $source, $config)
    {
        parent::__construct($config);
        $this->source = $source;
    }

    public function transform($value)
    {
        $map = $this->getMap();
        return $map[$value] ?? $value;
    }

    /**
     * Get website Id to website code map
     *
     * @return array
     */
    private function getMap(): ?array
    {
        if (!$this->map) {
            $this->map = $this->source->getReviewStatuses();
        }
        return $this->map;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Status Id To Status Code')->getText();
    }
}
