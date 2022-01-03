<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */

declare(strict_types=1);

namespace Amasty\CustomerImport\Api;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;

interface CustomerActionInterface
{
    /**
     * Sets customer action option
     *
     * @param string $name
     * @param mixed $value
     * @return CustomerActionInterface
     */
    public function setOption(string $name, $value): CustomerActionInterface;

    /**
     * Applies action to affected by import behavior customer entities
     *
     * @param BehaviorResultInterface $behaviorResult
     * @return void
     */
    public function execute(BehaviorResultInterface $behaviorResult): void;
}
