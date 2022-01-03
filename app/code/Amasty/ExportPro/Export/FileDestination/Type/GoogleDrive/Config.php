<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Export\FileDestination\Type\GoogleDrive;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const KEY = 'key';
    const FILE_PATH = 'file_path';
    const FILE_NAME = 'filename';

    public function getKey(): ?string
    {
        return $this->_getData(self::KEY);
    }

    public function setKey(string $key): ConfigInterface
    {
        return $this->setData(self::KEY, $key);
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
