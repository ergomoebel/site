<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Export\Action\Export\Order;

use Amasty\ExportCore\Api\ActionInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\OrderExport\Api\OrderActionInterface;
use Amasty\OrderExport\Model\ModuleType;
use Amasty\OrderExport\Model\Profile\OrderActionResolver;
use Amasty\OrderExport\Model\Profile\Repository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderActions implements ActionInterface
{
    /**
     * @var OrderActionResolver
     */
    private $orderActionResolver;

    /**
     * @var Repository
     */
    private $profileRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderActionInterface[]|null
     */
    protected $actionsToPerform = [];
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    public function __construct(
        OrderActionResolver $orderActionResolver,
        Repository $profileRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->orderActionResolver = $orderActionResolver;
        $this->profileRepository = $profileRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    public function initialize(ExportProcessInterface $exportProcess)
    {
        if ($exportProcess->getProfileConfig()->getModuleType() !== ModuleType::TYPE) {
            return;
        }
        $profile = $this->profileRepository->getById(
            (int)$exportProcess->getProfileConfig()->getExtensionAttributes()->getExternalId()
        );

        foreach ($profile->getOrderActions() as $key => $data) {
            if (!($data['options']['value'] ?? null)) {
                continue;
            }
            if ($orderAction = $this->orderActionResolver->resolve($key)) {
                $this->actionsToPerform[$key] = [
                    'actionType' => $orderAction,
                    'options' => $data['options'] ?? []
                ];
            }
        }
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        if (empty($this->actionsToPerform)) {
            return;
        }
        $orderIds = $this->getOrderIds($exportProcess);

        /** @var SearchCriteriaBuilder $criteriaBuilder */
        $criteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $criteriaBuilder->addFilter('entity_id', $orderIds, 'IN')->create();
        foreach ($this->orderRepository->getList($searchCriteria)->getItems() as $order) {
            foreach ($this->actionsToPerform as $action) {
                $action['actionType']->execute($order, $action['options']);
            }
            $this->orderRepository->save($order);
        }
    }

    private function getOrderIds(ExportProcessInterface $exportProcess): array
    {
        $orderIds = [];

        foreach ($exportProcess->getData() as $processData) {
            if (!($entityId = $processData['entity_id'] ?? null)) {
                continue;
            }
            $orderIds[] = $processData['entity_id'];
        }

        return $orderIds;
    }
}
