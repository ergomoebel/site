<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Dropbox;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const TOKEN = 'token';
    const FILE_PATH = 'file_path';
    const FILE_NAME = 'filename';

    public function getToken(): ?string
    {
        return $this->_getData(self::TOKEN);
    }

    public function setToken(string $token): ConfigInterface
    {
        return $this->setData(self::TOKEN, $token);
    }

    public function getFilePath(): ?string
    {
        return $this->_getData(self::FILE_PATH);
    }

    public function setFilePath(?string $filePath): ConfigInterface
    {
        return $this->setData(self::FILE_PATH, $filePath);
    }

    public function getFilename(): ?string
    {
        return $this->_getData(self::FILE_NAME);
    }

    public function setFilename(?string $fileName): ConfigInterface
    {
        return $this->setData(self::FILE_NAME, $fileName);
    }
}
