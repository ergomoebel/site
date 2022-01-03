<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\RowModifier\Product;

use Amasty\ImportCore\Api\Modifier\RowModifierInterface;
use Magento\Customer\Api\Data\GroupInterface;

class TierPrice implements RowModifierInterface
{
    public function transform(array &$row): array
    {
        if (isset($row['customer_group_id'])) {
            $customerGroupId = trim($row['customer_group_id']);
            if (empty($customerGroupId)) {
                return $row;
            }

            if ((int)$customerGroupId === GroupInterface::CUST_GROUP_ALL) {
                $row['customer_group_id'] = '0';
                $row['all_groups'] = '1';
            }
        }

        return $row;
    }
}
