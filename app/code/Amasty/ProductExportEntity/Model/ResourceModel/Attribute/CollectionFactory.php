<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Attribute;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection as AttributeCollection;
use Magento\Framework\ObjectManagerInterface;

class CollectionFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create attribute collection instance
     *
     * @param string $className
     * @return AttributeCollection
     */
    public function create($className)
    {
        $instance = $this->objectManager->create($className);
        if (!$instance instanceof AttributeCollection) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t inherit ' . AttributeCollection::class
            );
        }
        return $instance;
    }
}
