<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\AddUpdate;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Import\Behavior\AddUpdate\Table as AddUpdateDirect;

class ProductEntity extends AddUpdateDirect
{
    /**
     * @inheritDoc
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $this->offForeignKeysCheck();
        try {
            $result = parent::execute($data, $customIdentifier);
            $this->onForeignKeysCheck();
        } catch (\Exception $e) {
            $this->onForeignKeysCheck();
            throw $e;
        }

        return $result;
    }

    private function offForeignKeysCheck()
    {
        $this->getConnection()->query('SET FOREIGN_KEY_CHECKS = 0;');
    }

    private function onForeignKeysCheck()
    {
        $this->getConnection()->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
