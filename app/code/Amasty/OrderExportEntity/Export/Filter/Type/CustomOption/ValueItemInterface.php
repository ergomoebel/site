<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Export\Filter\Type\CustomOption;

/**
 * Filter value item data interface
 */
interface ValueItemInterface
{
    /**
     * @return string
     */
    public function getKey(): ?string;

    /**
     * @param string $key
     * @return ValueItemInterface
     */
    public function setKey(?string $key): ValueItemInterface;

    /**
     * @return string
     */
    public function getValue(): ?string;

    /**
     * @param string $value
     * @return ValueItemInterface
     */
    public function setValue(?string $value): ValueItemInterface;
}
