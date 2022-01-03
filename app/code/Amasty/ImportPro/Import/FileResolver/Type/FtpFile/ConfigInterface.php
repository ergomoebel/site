<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\FtpFile;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * @param string $host
     *
     * @return void
     */
    public function setHost(string $host): void;

    /**
     * @return bool
     */
    public function isPassiveMode(): bool;

    /**
     * @param bool $isPassiveMode
     *
     * @return void
     */
    public function setIsPassiveMode(bool $isPassiveMode): void;

    /**
     * @return string
     */
    public function getUser(): string;

    /**
     * @param string $user
     *
     * @return void
     */
    public function setUser(string $user): void;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @param string $password
     *
     * @return void
     */
    public function setPassword(string $password): void;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param string $path
     *
     * @return void
     */
    public function setPath(string $path): void;
}
