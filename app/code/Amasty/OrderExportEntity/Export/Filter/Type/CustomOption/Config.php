<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Export\Filter\Type\CustomOption;

class Config implements ConfigInterface
{
    /**
     * @var ValueItemInterface[]
     */
    private $valueItems;

    public function getValueItems(): ?array
    {
        return $this->valueItems;
    }

    public function setValueItems(?array $valueItems): ConfigInterface
    {
        $this->valueItems = $valueItems;
        return $this;
    }
}
