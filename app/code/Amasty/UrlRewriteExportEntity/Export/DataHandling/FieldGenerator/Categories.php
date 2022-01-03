<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteExportEntity
 */

declare(strict_types=1);

namespace Amasty\UrlRewriteExportEntity\Export\DataHandling\FieldGenerator;

use Amasty\Base\Model\Serializer;
use Amasty\ExportCore\Api\VirtualField\GeneratorInterface;
use Amasty\UrlRewriteExportEntity\Export\DataHandling\FieldModifier\Utils\CategoriesPathResolver;
use Magento\Store\Model\Store;

class Categories implements GeneratorInterface
{
    /**
     * @var CategoriesPathResolver
     */
    private $categoriesPathResolver;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        CategoriesPathResolver $categoriesPathResolver,
        Serializer $serializer
    ) {
        $this->categoriesPathResolver = $categoriesPathResolver;
        $this->serializer = $serializer;
    }

    public function generateValue(array $currentRecord)
    {
        if (isset($currentRecord['metadata'])
            && stripos($currentRecord['metadata'], 'category_id') !== false
        ) {
            $categoryId = $this->serializer->unserialize($currentRecord['metadata'])['category_id'] ?? null;
        } else {
            $categoryId = null;
        }

        if (isset($currentRecord['store_id'])) {
            $storeId = (int)$currentRecord['store_id'];
        } else {
            $storeId = Store::DEFAULT_STORE_ID;
        }

        if ($path = $this->categoriesPathResolver->getNamePathByEntityId((int)$categoryId, $storeId)) {
            return $path;
        }

        return '';
    }
}
