<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteExportEntity
 */

declare(strict_types=1);

namespace Amasty\UrlRewriteExportEntity\Export\DataHandling\FieldModifier\CmsPage;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;

class PageId2Identifier extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        array $config
    ) {
        parent::__construct($config);
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (!empty($value)) {
            if ($identifier = $this->getIdentifierByPageId((int)$value)) {
                return $identifier;
            }
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return __('Page Id To Identifier')->render();
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    /**
     * Get CMS page identifier by page_id
     *
     * @param int $pageId
     * @return string
     * @throws \Exception
     */
    private function getIdentifierByPageId(int $pageId): string
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('cms_page'), 'identifier')
            ->where('page_id = ?', $pageId);

        return (string)$connection->fetchOne($select);
    }

    /**
     * Get product entity connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection(): AdapterInterface
    {
        $metadata = $this->metadataPool->getMetadata(PageInterface::class);

        return $this->resourceConnection->getConnection(
            $metadata->getEntityConnectionName()
        );
    }
}
