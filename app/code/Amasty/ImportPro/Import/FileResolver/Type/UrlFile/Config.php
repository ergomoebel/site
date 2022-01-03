<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\UrlFile;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const URL = 'url';
    const USER = 'user';
    const PASSWORD = 'password';

    public function getUrl(): string
    {
        return $this->getData(self::URL);
    }

    public function setUrl(string $url): void
    {
        $this->setData(self::URL, $url);
    }
    public function getUser(): ?string
    {
        return $this->getData(self::USER);
    }

    public function setUser(?string $user): void
    {
        $this->setData(self::USER, $user);
    }

    //TODO encrypt
    public function getPassword(): ?string
    {
        return $this->getData(self::PASSWORD);
    }

    public function setPassword(?string $password): void
    {
        $this->setData(self::PASSWORD, $password);
    }
}
