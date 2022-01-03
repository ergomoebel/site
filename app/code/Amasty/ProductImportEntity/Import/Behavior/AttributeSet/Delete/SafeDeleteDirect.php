<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\AttributeSet\Delete;

use Amasty\Base\Model\Serializer;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ImportCore\Import\Behavior;
use Amasty\ImportCore\Import\Utils\DuplicateFieldChecker;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;

class SafeDeleteDirect extends Behavior\Delete\Table
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ResourceConnection $resourceConnection,
        Serializer $serializer,
        BehaviorResultInterfaceFactory $behaviorResultFactory,
        DuplicateFieldChecker $duplicateFieldChecker,
        ProductFactory $productFactory,
        array $config
    ) {
        parent::__construct(
            $objectManager,
            $resourceConnection,
            $serializer,
            $behaviorResultFactory,
            $duplicateFieldChecker,
            $config
        );
        $this->productFactory = $productFactory;
    }

    public function getUniqueIds(array &$data): array
    {
        return array_filter(parent::getUniqueIds($data), function ($attributeSetId) {
            // Prevent deletion of default attribute set
            return (int)$attributeSetId !== (int)$this->productFactory->create()->getDefaultAttributeSetId();
        });
    }
}
