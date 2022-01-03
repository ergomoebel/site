<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Xlsx;

interface ConfigInterface
{
    /**
     * @return bool|null
     */
    public function isHasHeaderRow(): ?bool;

    /**
     * @param bool|null $hasHeaderRow
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Xlsx\ConfigInterface
     */
    public function setHasHeaderRow(?bool $hasHeaderRow): ConfigInterface;

    /**
     * @return string|null
     */
    public function getPostfix(): ?string;

    /**
     * @param string|null $postfix
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Xlsx\ConfigInterface
     */
    public function setPostfix(?string $postfix): ConfigInterface;

    /**
     * @return bool|null
     */
    public function isCombineChildRows(): ?bool;

    /**
     * @param bool|null $combineChildRows
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Xlsx\ConfigInterface
     */
    public function setCombineChildRows(?bool $combineChildRows): ConfigInterface;

    /**
     * @return string|null
     */
    public function getChildRowSeparator(): ?string;

    /**
     * @param string|null $childRowSeparator
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Xlsx\ConfigInterface
     */
    public function setChildRowSeparator(?string $childRowSeparator): ConfigInterface;
}
