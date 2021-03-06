<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\Rest\Auth\Bearer;

class Config implements ConfigInterface
{
    /**
     * @var string
     */
    private $token;

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): ConfigInterface
    {
        $this->token = $token;

        return $this;
    }
}
