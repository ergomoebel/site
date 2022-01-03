<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\GoogleDrive;

interface ConfigInterface
{
    /**
     * @return string|null
     */
    public function getKey(): ?string;

    /**
     * @param string $key
     * @return \Amasty\ImportPro\Import\FileResolver\Type\GoogleDrive\ConfigInterface
     */
    public function setKey(string $key): ConfigInterface;

    /**
     * @return string|null
     */
    public function getFilePath(): ?string;

    /**
     * @param string|null $filePath
     * @return \Amasty\ImportPro\Import\FileResolver\Type\GoogleDrive\ConfigInterface
     */
    public function setFilePath(?string $filePath): ConfigInterface;
}
