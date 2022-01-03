<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Export\Filter\Type\CustomOption;

interface ConfigInterface
{
    /**
     * @return \Amasty\OrderExportEntity\Export\Filter\Type\CustomOption\ValueItemInterface[]|null
     */
    public function getValueItems(): ?array;

    /**
     * @param \Amasty\OrderExportEntity\Export\Filter\Type\CustomOption\ValueItemInterface[] $valueItems
     * @return ConfigInterface
     */
    public function setValueItems(?array $valueItems): ConfigInterface;
}
