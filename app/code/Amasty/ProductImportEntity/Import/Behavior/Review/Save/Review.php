<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Review\Save;

use Amasty\Base\Model\Serializer;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ImportCore\Api\BehaviorInterface;
use Amasty\ImportCore\Import\Behavior\Table;
use Amasty\ImportCore\Import\Utils\DuplicateFieldChecker;
use Amasty\ProductImportEntity\Import\DataHandling\SkuToProductId;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;

class Review extends Table implements BehaviorInterface
{
    const REVIEW_STORE_TABLE = 'review_store';

    /**
     * @var array
     */
    protected $config = ['tableName' => 'review'];

    /**
     * @var array
     */
    private $reviewDetailData = [
        'tableName' => 'review_detail',
        'fields' => ['title', 'detail', 'nickname', 'customer_id', 'store_id', 'review_id']
    ];

    /**
     * @var SkuToProductId
     */
    private $skuToProductId;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ResourceConnection $resourceConnection,
        Serializer $serializer,
        BehaviorResultInterfaceFactory $behaviorResultFactory,
        DuplicateFieldChecker $duplicateFieldChecker,
        SkuToProductId $skuToProductId,
        array $config
    ) {
        parent::__construct(
            $objectManager,
            $resourceConnection,
            $serializer,
            $behaviorResultFactory,
            $duplicateFieldChecker,
            array_merge($config, $this->config)
        );
        $this->skuToProductId = $skuToProductId;
    }

    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();

        $data = $this->skuToProductId->executeRows($data, 'entity_pk_value', 'product_sku');
        $preparedLinkData = $this->prepareData($data);

        if (!$this->hasDataToInsert($preparedLinkData)) {
            return $result;
        }

        $maxId = $this->getMaxId();
        $this->resourceConnection->getConnection()->insertOnDuplicate($this->getTable(), $preparedLinkData);
        $newIds = $this->getNewIds($maxId);
        $uniqueIds = $this->getUniqueIds($preparedLinkData);

        foreach ($uniqueIds as $index => $id) {
            $data[$index][$this->getIdField()] = $id;
        }

        $this->saveReviewDetails($data);
        $this->saveReviewStoreData($data);

        $result->setUpdatedIds(array_diff($uniqueIds, $newIds));
        $result->setNewIds($newIds);

        return $result;
    }

    private function saveReviewStoreData(array $data): void
    {
        $insertData = [];
        foreach ($data as $row) {
            if (isset($row['review_id'], $row['store_id'])) {
                $insertData[] = ['review_id' => $row['review_id'], 'store_id' => $row['store_id']];
                $insertData[] = ['review_id' => $row['review_id'], 'store_id' => 0];
            }
        }
        if ($insertData) {
            $tableName = $this->resourceConnection->getTableName(self::REVIEW_STORE_TABLE);
            $this->getConnection()->insertOnDuplicate($tableName, $insertData);
        }
    }

    private function saveReviewDetails(array $data): void
    {
        $insertData = [];
        foreach ($data as $row) {
            $reviewDetailsFields = array_flip($this->reviewDetailData['fields']);
            $insertData[] = $this->prepareDetailData(array_intersect_key($row, $reviewDetailsFields));
        }
        if ($insertData) {
            $tableName = $this->resourceConnection->getTableName($this->reviewDetailData['tableName']);
            $this->getConnection()->insertOnDuplicate($tableName, $insertData);
        }
    }

    private function prepareDetailData(array $detailData): array
    {
        if ($reviewId = (int)$detailData['review_id']) {
            $reviewIdSelect = $this->resourceConnection->getConnection()->select()
                ->from($this->resourceConnection->getTableName('review_detail'))
                ->columns(['detail_id'])
                ->where('review_id = ?', $reviewId);

            if ($reviewDetail = $this->resourceConnection->getConnection()->fetchRow($reviewIdSelect)) {
                $detailData['detail_id'] = $reviewDetail['detail_id'];
            }
        }

        if (!isset($detailData['detail_id'])) {
            $detailData['detail_id'] = null;
        }

        return $detailData;
    }
}
