<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */

declare(strict_types=1);

namespace Amasty\ProductImport\Api;

use Amasty\ImportCore\Api\ImportProcessInterface;

interface ProductActionInterface
{
    /**#@+
     * Action groups
     */
    const GROUP_BATCH = 'batch';
    const GROUP_FULL_SET = 'full_set';
    /**#@-*/

    /**
     * Adding an option for an action
     *
     * @param string $name
     * @param mixed $value
     * @return ProductActionInterface
     */
    public function setOption(string $name, $value): ProductActionInterface;

    /**
     * Applies action to affected product entities
     *
     * @param ImportProcessInterface $importProcess
     * @return void
     */
    public function execute(ImportProcessInterface $importProcess): void;
}
