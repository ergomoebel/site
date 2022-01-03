<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Observer;

use Amasty\OrderExportEntity\Model\AttributeFactory;
use Amasty\OrderExportEntity\Model\ResourceModel\Attribute as AttributeResource;
use Amasty\OrderExportEntity\Model\Indexer\Attribute\Processor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Eav\Api\Data\AttributeInterface;

class CatalogEntityAttributeSaveAfterObserver implements ObserverInterface
{
    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var AttributeResource
     */
    protected $attributeResource;

    /**
     * @var Processor
     */
    protected $productAttributesIndexerProcessor;

    public function __construct(
        AttributeFactory $attributeFactory,
        AttributeResource $attributeResource,
        Processor $productAttributesIndexerProcessor
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->attributeResource = $attributeResource;
        $this->productAttributesIndexerProcessor = $productAttributesIndexerProcessor;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(Observer $observer)
    {
        $catalogAttribute = $observer->getEvent()->getAttribute();

        if ($catalogAttribute) {
            $attribute = $this->attributeFactory->create();
            $this->attributeResource
                ->load($attribute, $catalogAttribute->getId(), AttributeInterface::ATTRIBUTE_ID);

            if ($catalogAttribute->getData('amasty_order_export_attribute_use_in_index')) {
                $attribute->addData([
                    AttributeInterface::ATTRIBUTE_ID => $catalogAttribute->getId(),
                    AttributeInterface::ATTRIBUTE_CODE => $catalogAttribute->getAttributeCode(),
                    AttributeInterface::FRONTEND_LABEL => $catalogAttribute->getFrontendLabel()
                ]);
                $this->attributeResource->save($attribute);
                $this->productAttributesIndexerProcessor->markIndexerAsInvalid();
            } else {
                $this->attributeResource->delete($attribute);
            }
        }
    }
}
