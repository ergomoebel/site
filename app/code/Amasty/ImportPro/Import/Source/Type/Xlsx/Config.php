<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\Source\Type\Xlsx;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const SETTING_PREFIX = '.';
    const SETTING_COMBINE_CHILD_ROWS = false;
    const SETTING_CHILD_ROW_SEPARATOR = ',';

    const PREFIX = 'postfix';
    const COMBINE_CHILD_ROWS = 'combine_child_rows';
    const CHILD_ROW_SEPARATOR = 'child_row_separator';

    public function getPrefix(): ?string
    {
        return $this->getData(self::PREFIX) ?? self::SETTING_PREFIX;
    }

    public function setPrefix(?string $postfix): ConfigInterface
    {
        $this->setData(self::PREFIX, $postfix);

        return $this;
    }

    public function isCombineChildRows(): ?bool
    {
        return $this->getData(self::COMBINE_CHILD_ROWS) ?? self::SETTING_COMBINE_CHILD_ROWS;
    }

    public function setCombineChildRows(?bool $combineChildRows): ConfigInterface
    {
        $this->setData(self::COMBINE_CHILD_ROWS, $combineChildRows);

        return $this;
    }

    public function getChildRowSeparator(): ?string
    {
        return $this->getData(self::CHILD_ROW_SEPARATOR) ?? self::SETTING_CHILD_ROW_SEPARATOR;
    }

    public function setChildRowSeparator(?string $childRowSeparator): ConfigInterface
    {
        $this->setData(self::CHILD_ROW_SEPARATOR, $childRowSeparator);

        return $this;
    }
}
