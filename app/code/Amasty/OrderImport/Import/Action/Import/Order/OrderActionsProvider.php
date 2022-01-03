<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Action\Import\Order;

use Amasty\OrderImport\Api\OrderActionInterface;

class OrderActionsProvider
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

    public function getSortedActions(): array
    {
        $sortedActions = [];

        foreach ($this->actions as $key => $actionData) {
            if (!$this->isActionDataValid($actionData)) {
                continue;
            }
            $sortedActions[$actionData['sortOrder']][$key] = $actionData['action'];
        }
        ksort($sortedActions);

        $sortedActions = array_merge(...$sortedActions);

        return $sortedActions;
    }

    private function isActionDataValid(array $action): bool
    {
        return !(empty($action['action'])
            || !($action['action'] instanceof OrderActionInterface)
            || !isset($action['sortOrder']));
    }
}
