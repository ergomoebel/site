<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\DataHandling\FieldModifier\SourceOption;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Magento\Framework\Data\OptionSourceInterface;

abstract class Value2Label extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var array|null
     */
    private $map;

    public function transform($value)
    {
        $map = $this->getMap();

        return $map[$value] ?? $value;
    }

    /**
     * Get source model
     *
     * @return OptionSourceInterface
     */
    abstract protected function getSourceModel();

    /**
     * Get option value to option label map
     *
     * @return array
     */
    protected function getMap()
    {
        if (!$this->map) {
            $this->map = [];

            $options = $this->getSourceModel()
                ->toOptionArray();
            foreach ($options as $option) {
                $this->map[$option['value']] = $option['label'];
            }
        }

        return $this->map;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }
}
