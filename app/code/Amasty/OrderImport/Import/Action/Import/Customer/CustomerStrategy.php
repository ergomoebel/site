<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Action\Import\Customer;

use Amasty\ImportCore\Import\Source\SourceDataStructure;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;

class CustomerStrategy
{
    const SUB_ENTITY_KEYS = [
        'sales_shipment',
        'sales_order_billing_address',
        'sales_order_shipping_address'
    ];

    /**
     * @var CustomerResource
     */
    private $customerResource;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var string
     */
    private $strategyToPerform;

    public function __construct(
        CustomerResource $customerResource,
        CustomerInterfaceFactory $customerFactory,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        State $appState,
        string $strategyToPerform
    ) {
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->accountManagement = $accountManagement;
        $this->appState = $appState;
        $this->strategyToPerform = $strategyToPerform;
        $this->customerResource = $customerResource;
    }

    public function execute(array &$importOrderData): void
    {
        if ($method = $this->strategyToPerform) {
            $this->$method($importOrderData);
        }
    }

    private function guestOrderIfNoCustomer(array &$importOrderData): void
    {
        if ($customerId = $this->retrieveCustomerId($importOrderData)) {
            $this->assignCustomerToOrder($importOrderData, $customerId);
        } else {
            $this->assignGuestToOrder($importOrderData);
        }
    }

    private function createCustomerIfNoCustomer(array &$importOrderData): void
    {
        if ($this->isGuest($importOrderData)) {
            return;
        }

        if (!($customerId = $this->retrieveCustomerId($importOrderData))) {
            $customer = $this->createNewCustomer($importOrderData);
            $customerId = (int)$customer->getId();
        }

        $this->assignCustomerToOrder($importOrderData, $customerId);
    }

    private function isGuest(array $importOrderData): bool
    {
        if ((array_key_exists('customer_is_guest', $importOrderData)
                && $importOrderData['customer_is_guest'] == true)
            || (array_key_exists('customer_id', $importOrderData)
                && $importOrderData['customer_id'] == 0)
        ) {
            return true;
        }

        return false;
    }

    private function retrieveCustomerId(array $importOrderData): ?int
    {
        $connection = $this->customerResource->getConnection();
        $selectExpr = '';
        if (isset($importOrderData['customer_id'])) {
            $selectExpr .= '(entity_id = ' . $importOrderData['customer_id'] . ')';
        }
        if (isset($importOrderData['customer_email'])) {
            if (!empty($selectExpr)) {
                $selectExpr .= ' OR (email = \'' . $importOrderData['customer_email'] . '\')';
            } else {
                $selectExpr .= '(email = \'' . $importOrderData['customer_email'] . '\')';
            }
        }
        if (empty($selectExpr)) {
            return null;
        }

        $customerSelect = $connection->select()->from(
            $this->customerResource->getEntityTable(),
            ['entity_id']
        )->where(
            new \Zend_Db_Expr('(' . $selectExpr . ')')
        );

        if ($storeId = $importOrderData['store_id'] ?? null) {
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            $customerSelect->where(
                'website_id = ?',
                $websiteId
            );
        }

        return (int)$connection->fetchOne($customerSelect);
    }

    private function assignGuestToOrder(array &$importOrderData): void
    {
        $importOrderData['customer_id'] = null;
        $importOrderData['customer_is_guest'] = true;
        $importOrderData['customer_group_id'] = Group::NOT_LOGGED_IN_ID;

        $this->processSubEntities($importOrderData);
    }

    private function assignCustomerToOrder(array &$importOrderData, int $customerId): void
    {
        $importOrderData['customer_id'] = $customerId;
        $importOrderData['customer_is_guest'] = false;

        $this->processSubEntities($importOrderData, $customerId);
    }

    private function createNewCustomer(array $importOrderData): CustomerInterface
    {
        if (isset($importOrderData['store_id'])) {
            $websiteId = $this->storeManager->getStore($importOrderData['store_id'])->getWebsiteId();
        } else {
            $websiteId = $this->storeManager->getDefaultStoreView()->getWebsiteId();
        }
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);

        $customer->setEmail($importOrderData['customer_email'] ?? '');
        $customer->setFirstname($importOrderData['customer_firstname'] ?? '');
        $customer->setLastname($importOrderData['customer_lastname'] ?? '');
        $customer->setMiddlename($importOrderData['customer_middlename'] ?? '');
        $customer->setPrefix($importOrderData['customer_prefix'] ?? '');
        $customer->setSuffix($importOrderData['customer_suffix'] ?? '');
        $customer->setGroupId($importOrderData['customer_group_id'] ?? null);

        //need to emulate frontend area because of theme full path problem
        // in \Magento\Framework\View\Design\Fallback\Rule\Theme::getPatternDirs
        return $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$this->accountManagement, 'createAccount'],
            [$customer]
        );
    }

    private function processSubEntities(array &$importOrderData, ?int $customerId = null)
    {
        if (!isset($importOrderData[SourceDataStructure::SUB_ENTITIES_DATA_KEY])) {
            return;
        }

        foreach ($importOrderData[SourceDataStructure::SUB_ENTITIES_DATA_KEY] as $subEntityKey => &$data) {
            if (in_array($subEntityKey, self::SUB_ENTITY_KEYS)) {
                foreach ($data as &$subEntityData) {
                    $subEntityData['customer_id'] = $customerId;
                }
            }
        }
    }
}
