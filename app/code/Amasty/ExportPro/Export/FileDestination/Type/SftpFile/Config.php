<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Export\FileDestination\Type\SftpFile;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const HOST = 'host';
    const USER = 'user';
    const PASSWORD = 'password';
    const PATH = 'path';
    const FILE_NAME = 'filename';

    public function getHost(): ?string
    {
        return $this->getData(self::HOST);
    }

    public function setHost(?string $host): ConfigInterface
    {
        $this->setData(self::HOST, $host);

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->getData(self::USER);
    }

    public function setUser(?string $user): ConfigInterface
    {
        $this->setData(self::USER, $user);

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->getData(self::PASSWORD);
    }

    public function setPassword(?string $password): ConfigInterface
    {
        $this->setData(self::PASSWORD, $password);

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->getData(self::PATH);
    }

    public function setPath(?string $path): ConfigInterface
    {
        $this->setData(self::PATH, $path);

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->getData(self::FILE_NAME);
    }

    public function setFilename(?string $filename): ConfigInterface
    {
        $this->setData(self::FILE_NAME, $filename);

        return $this;
    }
}
