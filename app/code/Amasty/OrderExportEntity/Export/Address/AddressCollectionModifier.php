<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */


namespace Amasty\OrderExportEntity\Export\Address;

use Amasty\ExportCore\Api\CollectionModifierInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;

class AddressCollectionModifier implements CollectionModifierInterface
{
    /**
     * @var string
     */
    private $addressType;

    public function __construct($config)
    {
        if (empty($config['address_type'])) {
            throw new \LogicException('Address Type is not set for AddressCollectionModifier');
        }

        $this->addressType = $config['address_type'];
    }

    public function apply(\Magento\Framework\Data\Collection $collection): CollectionModifierInterface
    {
        $collection->addFieldToFilter(OrderAddressInterface::ADDRESS_TYPE, $this->addressType);

        return $this;
    }
}
