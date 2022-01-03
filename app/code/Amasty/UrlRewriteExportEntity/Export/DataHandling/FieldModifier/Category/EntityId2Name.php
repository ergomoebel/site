<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteExportEntity
 */

declare(strict_types=1);

namespace Amasty\UrlRewriteExportEntity\Export\DataHandling\FieldModifier\Category;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Amasty\UrlRewriteExportEntity\Export\DataHandling\FieldModifier\Utils\CategoriesPathResolver;
use Magento\Store\Model\Store;

class EntityId2Name extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var CategoriesPathResolver
     */
    private $categoriesPathResolver;

    /**
     * Current row storeId
     *
     * @var int
     */
    private $storeId;

    public function __construct(
        CategoriesPathResolver $categoriesPathResolver,
        array $config = []
    ) {
        parent::__construct($config);
        $this->categoriesPathResolver = $categoriesPathResolver;
    }

    public function prepareRowOptions(array $row)
    {
        if (isset($row['store_id'])) {
            $this->storeId = (int)$row['store_id'];
        } else {
            $this->storeId = Store::DEFAULT_STORE_ID;
        }
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (!empty($value)) {
            if ($path = $this->categoriesPathResolver->getNamePathByEntityId((int)$value, $this->storeId)) {
                return $path;
            }
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return __('Category Entity Id To Slug')->render();
    }
}
