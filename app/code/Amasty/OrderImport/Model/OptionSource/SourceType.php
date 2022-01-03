<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Model\OptionSource;

use Amasty\ImportCore\Api\Source\SourceConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;

class SourceType implements OptionSourceInterface
{
    /**
     * @var SourceConfigInterface
     */
    private $sourceConfig;

    public function __construct(
        SourceConfigInterface $sourceConfig
    ) {
        $this->sourceConfig = $sourceConfig;
    }

    public function toOptionArray()
    {
        $result = [];

        foreach ($this->sourceConfig->all() as $code => $sourceConfig) {
            $result[] = [
                'value' => $code,
                'label' => $sourceConfig['name']
            ];
        }

        return $result;
    }
}
