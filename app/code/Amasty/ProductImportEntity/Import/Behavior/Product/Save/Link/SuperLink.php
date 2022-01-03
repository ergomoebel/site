<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\Link;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Magento\Catalog\Model\ResourceModel\Product\Relation;
use Magento\Framework\App\ResourceConnection;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link;
use Magento\Store\Model\StoreManagerInterface;

class SuperLink extends AbstractLink
{
    /**
     * @var Relation
     */
    private $relationProcessor;

    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        BehaviorResultInterfaceFactory $resultFactory,
        Relation $relationProcessor
    ) {
        parent::__construct(
            $resourceConnection,
            $storeManager,
            $resultFactory
        );
        $this->relationProcessor = $relationProcessor;
    }

    /**
     * @inheritDoc
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = parent::execute($data, $customIdentifier);
        $this->addRelations($data);

        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function getLinkTypeId()
    {
        return Link::LINK_TYPE_GROUPED;
    }

    /**
     * Add product relations
     *
     * @param array $data
     * @return void
     */
    private function addRelations(array $data)
    {
        foreach ($data as $row) {
            if (isset($row['product_id']) && isset($row['linked_product_id'])) {
                $this->relationProcessor->addRelation(
                    $row['product_id'],
                    $row['linked_product_id']
                );
            }
        }
    }
}
