<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Action\Import\Order;

use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\OrderImport\Api\OrderActionInterface;
use Amasty\OrderImport\Model\ModuleType;
use Amasty\OrderImport\Model\Profile\Repository;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderActions implements ActionInterface
{
    /**
     * @var OrderActionsProvider
     */
    private $orderActionsProvider;

    /**
     * @var Repository
     */
    private $profileRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var OrderActionInterface[]
     */
    protected $actionsToPerform = [];

    public function __construct(
        OrderActionsProvider $orderActionsProvider,
        Repository $profileRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->orderActionsProvider = $orderActionsProvider;
        $this->profileRepository = $profileRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    public function initialize(ImportProcessInterface $importProcess): void
    {
        if ($importProcess->getProfileConfig()->getModuleType() !== ModuleType::TYPE) {
            return;
        }
        $profile = $this->profileRepository->getById(
            (int)$importProcess->getProfileConfig()->getExtensionAttributes()->getExternalId()
        );

        $this->actionsToPerform = $this->getSortedOrderActions($profile->getOrderActions());
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        $orderResult = $importProcess->getProcessedEntityResult(
            $importProcess->getProfileConfig()->getEntityCode()
        );

        if (empty($this->actionsToPerform) || empty($orderResult->getAffectedIds())) {
            return;
        }

        $newIds = $orderResult->getNewIds();
        $criteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $criteriaBuilder->addFilter('entity_id', $orderResult->getAffectedIds(), 'IN')->create();
        foreach ($this->orderRepository->getList($searchCriteria)->getItems() as $order) {
            foreach ($this->actionsToPerform as $action) {
                $action['options']['isNew'] = in_array($order->getId(), $newIds);
                $action['actionType']->execute($order, $action['options']);
            }
            $this->orderRepository->save($order);
        }
    }

    private function getSortedOrderActions(array $orderActionsData): array
    {
        $sortedActions = $sortedActions = [];
        $orderActions = $this->orderActionsProvider->getSortedActions();
        $sortOrder = array_keys($orderActions);

        foreach ($orderActionsData as $key => $data) {
            if (!($data['options']['value'] ?? null)) {
                continue;
            }
            if (key_exists($key, $orderActions)) {
                $sortedActions[array_search($key, $sortOrder)][$key] = [
                    'actionType' => $orderActions[$key],
                    'options' => $data['options'] ?? []
                ];
            }
        }
        ksort($sortedActions);

        return $sortedActions ? array_merge(...$sortedActions) : [];
    }
}
