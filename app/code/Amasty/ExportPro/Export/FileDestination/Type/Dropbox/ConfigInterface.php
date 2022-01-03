<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Dropbox;

interface ConfigInterface
{
    /**
     * @return string|null
     */
    public function getToken(): ?string;

    /**
     * @param string $token
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Dropbox\ConfigInterface
     */
    public function setToken(string $token): ConfigInterface;

    /**
     * @return string|null
     */
    public function getFilePath(): ?string;

    /**
     * @param string|null $filePath
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Dropbox\ConfigInterface
     */
    public function setFilePath(?string $filePath): ConfigInterface;

    /**
     * @return string|null
     */
    public function getFilename(): ?string;

    /**
     * @param string|null $fileName
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Dropbox\ConfigInterface
     */
    public function setFilename(?string $fileName): ConfigInterface;
}
