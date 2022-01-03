<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Twig;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const HEADER = 'header';
    const CONTENT = 'content';
    const FOOTER = 'footer';
    const SEPARATOR = 'separator';
    const EXTENSION = 'extension';
    const TEMPLATE = 'template';

    public function getHeader(): ?string
    {
        return $this->getData(self::HEADER);
    }

    public function setHeader(?string $header): ConfigInterface
    {
        $this->setData(self::HEADER, $header);

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->getData(self::CONTENT);
    }

    public function setContent(?string $content): ConfigInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    public function getFooter(): ?string
    {
        return $this->getData(self::FOOTER);
    }

    public function setFooter(?string $footer): ConfigInterface
    {
        $this->setData(self::FOOTER, $footer);

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->getData(self::SEPARATOR);
    }

    public function setSeparator(?string $separator): ConfigInterface
    {
        $this->setData(self::SEPARATOR, $separator);

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->getData(self::EXTENSION);
    }

    public function setExtension(?string $extension): ConfigInterface
    {
        $this->setData(self::EXTENSION, $extension);

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->getData(self::TEMPLATE);
    }

    public function setTemplate(?string $template): ConfigInterface
    {
        $this->setData(self::TEMPLATE, $template);

        return $this;
    }
}
