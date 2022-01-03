<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Model\Profile;

use Amasty\OrderExport\Api\OrderActionInterface;

class OrderActionResolver
{
    /**
     * @var array
     */
    protected $actions;

    public function __construct(
        array $actions = []
    ) {
        $this->actions = $actions;
    }

    public function resolve(string $type = ''): ?OrderActionInterface
    {
        $action = null;

        if (key_exists($type, $this->actions)) {
            $action = $this->actions[$type];
        }

        return $action;
    }
}
