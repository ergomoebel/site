<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImportEntity
 */


declare(strict_types=1);

namespace Amasty\CustomerImportEntity\Import\Behavior\Delete\CustomerGroup;

use Amasty\ImportCore\Import\Behavior;

class SafeDeleteDirect extends Behavior\Delete\Table
{
    public function getUniqueIds(array &$data)
    {
        return array_filter(parent::getUniqueIds($data), function ($groupId) {
            return (int)$groupId !== 1; // Prevent deletion of General customer group
        });
    }
}
