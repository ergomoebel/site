<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Twig;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getHeader(): ?string;

    /**
     * @param string|null $header
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Twig\ConfigInterface
     */
    public function setHeader(?string $header): ConfigInterface;

    /**
     * @return string
     */
    public function getContent(): ?string;

    /**
     * @param string|null $content
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Twig\ConfigInterface
     */
    public function setContent(?string $content): ConfigInterface;

    /**
     * @return string|null
     */
    public function getFooter(): ?string;

    /**
     * @param string|null $footer
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Twig\ConfigInterface
     */
    public function setFooter(?string $footer): ConfigInterface;

    /**
     * @return string|null
     */
    public function getSeparator(): ?string;

    /**
     * @param string|null $separator
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Twig\ConfigInterface
     */
    public function setSeparator(?string $separator): ConfigInterface;

    /**
     * @return string|null
     */
    public function getExtension(): ?string;

    /**
     * @param string|null $extension
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Twig\ConfigInterface
     */
    public function setExtension(?string $extension): ConfigInterface;

    /**
     * @return string|null
     */
    public function getTemplate(): ?string;

    /**
     * @param string|null $template
     *
     * @return \Amasty\ExportPro\Export\Template\Type\Twig\ConfigInterface
     */
    public function setTemplate(?string $template): ConfigInterface;
}
