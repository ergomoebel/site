<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */

declare(strict_types=1);

namespace Amasty\ProductImport\Import\Action\Import\Product;

use Amasty\ProductImport\Api\ProductActionInterface;
use Amasty\ProductImport\Api\Data\ProfileInterface;

class ProductActionProvider
{
    /**
     * @var array
     */
    private $groups;

    /**
     * @var array
     */
    private $sortedActionsByGroup = [];

    public function __construct(array $groups = [])
    {
        $this->groups = $groups;
    }

    /**
     * Get product actions for specified profile
     *
     * @param ProfileInterface $profile
     * @param string $group
     * @return ProductActionInterface[]
     */
    public function getActions(
        ProfileInterface $profile,
        string $group = ProductActionInterface::GROUP_BATCH
    ): array {
        $profileActions = $profile->getProductActions();
        if (!$profileActions) {
            return [];
        }

        $result = [];
        foreach ($this->getSortedActions($group) as $actionName => $actionInstance) {
            if (isset($profileActions[$actionName])) {
                $options = $profileActions[$actionName]['options'] ?? [];
                if (!($options['value'] ?? null)) {
                    continue;
                }

                array_walk(
                    $options,
                    function ($optionValue, $optionKey) use ($actionInstance) {
                        $actionInstance->setOption($optionKey, $optionValue);
                    }
                );

                $result[] = $actionInstance;
            }
        }

        return $result;
    }

    /**
     * Get sorted action instances for specified group
     *
     * @param string $group
     * @return ProductActionInterface[]
     */
    private function getSortedActions(string $group): array
    {
        if (!isset($this->sortedActionsByGroup[$group])) {
            if (!isset($this->groups[$group])) {
                throw new \LogicException('Product action group ' . $group . ' is not specified.');
            }

            $sortedActions = [];
            foreach ($this->groups[$group] as $key => $action) {
                $this->assertActionDataValid($key, $action);
                $sortedActions[$action['sortOrder']][$key] = $action['class'];
            }
            ksort($sortedActions);

            $this->sortedActionsByGroup[$group] = array_merge(...$sortedActions);
        }

        return $this->sortedActionsByGroup[$group];
    }

    /**
     * Assert that action data is valid
     *
     * @param string $actionName
     * @param array $actionData
     * @return void
     */
    private function assertActionDataValid(string $actionName, array $actionData)
    {
        if (!isset($actionData['class'])) {
            throw new \LogicException('\'class\' is not specified for ' . $actionName . ' product action');
        }
        if (!$actionData['class'] instanceof ProductActionInterface) {
            throw new \LogicException(
                'Product action class ' . $actionData['class'] . ' doesn\'t implement '
                . ProductActionInterface::class
            );
        }
        if (!isset($actionData['sortOrder'])) {
            throw new \LogicException('\'sortOrder\' is not specified for ' . $actionName . ' action');
        }
    }
}
