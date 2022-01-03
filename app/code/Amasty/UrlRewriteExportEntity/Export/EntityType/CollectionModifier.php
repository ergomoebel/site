<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteExportEntity
 */

declare(strict_types=1);

namespace Amasty\UrlRewriteExportEntity\Export\EntityType;

use Amasty\ExportCore\Api\CollectionModifierInterface;
use Magento\Framework\Data\Collection;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class CollectionModifier implements CollectionModifierInterface
{
    /**
     * @var string
     */
    private $entityType;

    public function __construct(array $config)
    {
        if (empty($config['entity_type'])) {
            throw new \LogicException('Entity Type is not set for CollectionModifier');
        }

        $this->entityType = $config['entity_type'];
    }

    public function apply(Collection $collection): CollectionModifierInterface
    {
        $collection->addFieldToFilter(UrlRewrite::ENTITY_TYPE, $this->entityType);

        return $this;
    }
}
