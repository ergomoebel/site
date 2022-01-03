<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Export\Filter\Type\CustomOption;

use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Filter\FilterInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\DB\Select;

/**
 * Custom option filter
 */
class Filter implements FilterInterface
{
    const TYPE_ID = 'custom_option';

    public function apply(Collection $collection, FieldFilterInterface $filter)
    {
        /** @var ConfigInterface $config */
        $config = $filter->getExtensionAttributes()->getCustomOptionFilter();
        if ($config) {
            $conditions = [];
            $filterCondition = $filter->getCondition() ?? 'eq';

            /** @var \Magento\Framework\Data\Collection\AbstractDb $collection */
            $connection = $collection->getConnection();
            foreach ($config->getValueItems() as $valueItem) {
                $valueItemConditions = [
                    $connection->prepareSqlCondition('option_title', $valueItem->getKey()),
                    $connection->prepareSqlCondition(
                        'option_value',
                        [$filterCondition => $valueItem->getValue()]
                    )
                ];
                $conditions[] = implode(' ' . Select::SQL_AND . ' ', $valueItemConditions);
            }

            $collection->getSelect()
                ->where(
                    '(' . implode(') ' . Select::SQL_OR . ' (', $conditions) . ')',
                    null,
                    Select::TYPE_CONDITION
                );
        }
    }
}
