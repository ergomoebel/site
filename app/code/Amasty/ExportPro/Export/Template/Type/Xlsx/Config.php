<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Xlsx;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const SETTING_HAS_HEADER_ROW = true;
    const SETTING_POSTFIX = '.';
    const SETTING_COMBINE_CHILD_ROWS = false;
    const SETTING_CHILD_ROW_SEPARATOR = ',';

    const HAS_HEADER_ROW = 'has_header_row';
    const POSTFIX = 'postfix';
    const COMBINE_CHILD_ROWS = 'combine_child_rows';
    const CHILD_ROW_SEPARATOR = 'child_row_separator';

    public function isHasHeaderRow(): ?bool
    {
        return $this->getData(self::HAS_HEADER_ROW) ?? self::SETTING_HAS_HEADER_ROW;
    }

    public function setHasHeaderRow(?bool $hasHeaderRow): ConfigInterface
    {
        $this->setData(self::HAS_HEADER_ROW, $hasHeaderRow);

        return $this;
    }

    public function getPostfix(): ?string
    {
        return $this->getData(self::POSTFIX) ?? self::SETTING_POSTFIX;
    }

    public function setPostfix(?string $postfix): ConfigInterface
    {
        $this->setData(self::POSTFIX, $postfix);

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
