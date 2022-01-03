<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Json;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getHeader(): ?string;

    /**
     * @param string|null $header
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Json\ConfigInterface
     */
    public function setHeader(?string $header): ConfigInterface;

    /**
     * @return string|null
     */
    public function getFooter(): ?string;

    /**
     * @param string|null $footer
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Json\ConfigInterface
     */
    public function setFooter(?string $footer): ConfigInterface;
}
