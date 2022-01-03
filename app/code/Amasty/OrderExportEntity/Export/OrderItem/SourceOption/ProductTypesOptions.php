<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */


namespace Amasty\OrderExportEntity\Export\OrderItem\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Api\ProductTypeListInterface;

class ProductTypesOptions implements OptionSourceInterface
{
    /**
     * @var ProductTypeListInterface
     */
    private $productTypeList;

    public function __construct(ProductTypeListInterface $productTypeList)
    {
        $this->productTypeList = $productTypeList;
    }

    public function toOptionArray(): array
    {
        $result = [];
        if ($productTypes = $this->productTypeList->getProductTypes()) {
            foreach ($productTypes as $productType) {
                $result[] = ['value' => $productType->getName(), 'label' => $productType->getLabel()];
            }
        }

        return $result;
    }
}
