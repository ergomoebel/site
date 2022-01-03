<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteImportEntity
 */


declare(strict_types=1);

namespace Amasty\UrlRewriteImportEntity\Import\DataHandling\RowModifier\UrlRewrite;

use Amasty\ImportCore\Api\Modifier\RowModifierInterface;
use Magento\Cms\Api\GetPageByIdentifierInterface;
use Magento\CmsUrlRewrite\Model\CmsPageUrlRewriteGenerator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;

class CmsPage implements RowModifierInterface
{
    const PAGE_IDENTIFIER_FIELD_NAME = 'page_identifier';
    const STORE_ID_FIELD_NAME = 'store_id';

    /**
     * @var GetPageByIdentifierInterface
     */
    private $pageByIdentifier;

    public function __construct(GetPageByIdentifierInterface $pageByIdentifier)
    {
        $this->pageByIdentifier = $pageByIdentifier;
    }

    /**
     * @inheritDoc
     */
    public function transform(array &$row): array
    {
        $row['entity_type'] = CmsPageUrlRewriteGenerator::ENTITY_TYPE;

        if (isset($row[self::PAGE_IDENTIFIER_FIELD_NAME])) {
            $pageId = $this->getPageIdByIdentifier(
                $row[self::PAGE_IDENTIFIER_FIELD_NAME],
                $row[self::STORE_ID_FIELD_NAME] ?? Store::DEFAULT_STORE_ID
            );
            $row[self::PAGE_IDENTIFIER_FIELD_NAME] = $pageId;
            if ($pageId) {
                $row['entity_id'] = $pageId;
                $row['target_path'] = 'cms/page/view/page_id/' . $pageId;
            }
        }

        return $row;
    }

    /**
     * Get cms page Id using identifier
     *
     * @param string $identifier
     * @param int $storeId
     * @return int|null
     */
    private function getPageIdByIdentifier(string $identifier, $storeId)
    {
        try {
            $page = $this->pageByIdentifier->execute($identifier, (int)$storeId);

            return $page->getId();
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }
}
