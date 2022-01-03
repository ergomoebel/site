<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Rest\Auth;

class AuthConfig
{
    /**
     * @var array
     */
    private $authConfig = [];

    public function __construct(array $authConfig)
    {
        foreach ($authConfig as $config) {
            if (!isset($config['code'], $config['authClass'])) {
                throw new \LogicException('Rest API auth class "' . $config['code'] . ' is not configured properly');
            }
            $this->authConfig[$config['code']] = $config;
        }
    }

    public function get(string $type): array
    {
        if (!isset($this->authConfig[$type])) {
            throw new \RuntimeException('Rest Auth "' . $type . '" is not defined');
        }

        return $this->authConfig[$type];
    }

    public function all(): array
    {
        return $this->authConfig;
    }
}
