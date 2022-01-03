<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */

declare(strict_types=1);

namespace Amasty\CustomerImport\Import\Action\Import\Customer\CustomerActions;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\CustomerImport\Api\CustomerActionInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;

abstract class AbstractAction implements CustomerActionInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var array
     */
    protected $options = [];

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function setOption(string $name, $value): CustomerActionInterface
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Get customer entities
     *
     * @param BehaviorResultInterface $behaviorResult
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCustomers(BehaviorResultInterface $behaviorResult)
    {
        $searchCriteria = $this->getSearchCriteria($behaviorResult);
        $searchResult = $this->customerRepository->getList($searchCriteria);

        return $searchResult->getItems();
    }

    /**
     * Returns search criteria instances for retrieving customer entities
     *
     * @param BehaviorResultInterface $behaviorResult
     * @return SearchCriteriaInterface
     */
    abstract protected function getSearchCriteria(
        BehaviorResultInterface $behaviorResult
    ): SearchCriteriaInterface;
}
