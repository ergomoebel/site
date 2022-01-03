<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */

declare(strict_types=1);

namespace Amasty\CustomerImport\Import\Action\Import\Customer\CustomerActions;

use Amasty\CustomerImport\Model\OptionSource\CustomerStore;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;

class NotifyNewAccount extends AbstractAction
{
    /**
     * @var EmailNotificationInterface
     */
    private $emailNotification;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        EmailNotificationInterface $emailNotification,
        State $appState
    ) {
        parent::__construct($customerRepository, $searchCriteriaBuilder);
        $this->emailNotification = $emailNotification;
        $this->appState = $appState;
    }

    /**
     * @inheritdoc
     */
    public function execute(BehaviorResultInterface $behaviorResult): void
    {
        if (!$behaviorResult->getNewIds()) {
            return;
        }

        $customers = $this->getCustomers($behaviorResult);
        foreach ($customers as $customer) {
            $this->appState->emulateAreaCode(
                Area::AREA_FRONTEND,
                [$this->emailNotification, 'newAccount'],
                [
                    $customer,
                    EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD,
                    '',
                    $this->getStoreId()
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCriteria(BehaviorResultInterface $behaviorResult): SearchCriteriaInterface
    {
        return $this->searchCriteriaBuilder->addFilter('entity_id', $behaviorResult->getNewIds(), 'in')
            ->create();
    }

    /**
     * Get resolved store Id
     *
     * @return int|null
     */
    private function getStoreId()
    {
        $storeId = (int)$this->options['send_email_store_id'] ?? null;

        if ($storeId == CustomerStore::NO_SELECTION) {
            $storeId = null;
        }

        return $storeId;
    }
}
