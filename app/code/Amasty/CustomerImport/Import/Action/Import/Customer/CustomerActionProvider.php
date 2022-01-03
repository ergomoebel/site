<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */

declare(strict_types=1);

namespace Amasty\CustomerImport\Import\Action\Import\Customer;

use Amasty\CustomerImport\Api\CustomerActionInterface;
use Amasty\CustomerImport\Api\Data\ProfileInterface;

class CustomerActionProvider
{
    /**
     * @var array
     */
    private $actions;

    /**
     * @var array
     */
    private $sortedActions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * Get customer actions for specified profile
     *
     * @param ProfileInterface $profile
     * @return CustomerActionInterface[]
     */
    public function getActions(ProfileInterface $profile): array
    {
        $profileActions = $profile->getCustomerActions();
        if (!$profileActions) {
            return [];
        }

        $result = [];
        foreach ($this->getSortedActions() as $actionName => $actionInstance) {
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
     * Get sorted action instances
     *
     * @return CustomerActionInterface[]
     */
    private function getSortedActions(): array
    {
        if (empty($this->sortedActions)) {
            foreach ($this->actions as $key => $action) {
                $this->assertActionDataValid($key, $action);
                $this->sortedActions[$action['sortOrder']][$key] = $action['class'];
            }
            ksort($this->sortedActions);

            $this->sortedActions = array_merge(...$this->sortedActions);
        }

        return $this->sortedActions;
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
            throw new \LogicException('\'class\' is not specified for ' . $actionName . ' customer action');
        }
        if (!$actionData['class'] instanceof CustomerActionInterface) {
            throw new \LogicException(
                'Customer action class ' . $actionData['class'] . ' doesn\'t implement '
                . CustomerActionInterface::class
            );
        }
        if (!isset($actionData['sortOrder'])) {
            throw new \LogicException('\'sortOrder\' is not specified for ' . $actionName . ' action');
        }
    }
}
