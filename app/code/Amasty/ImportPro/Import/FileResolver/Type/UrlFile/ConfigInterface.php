<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\UrlFile;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @param string $url
     *
     * @return void
     */
    public function setUrl(string $url): void;

    /**
     * @return string|null
     */
    public function getUser(): ?string;

    /**
     * @param string|null $user
     *
     * @return void
     */
    public function setUser(?string $user): void;

    /**
     * @return string|null
     */
    public function getPassword(): ?string;

    /**
     * @param string|null $password
     *
     * @return void
     */
    public function setPassword(?string $password): void;
}
