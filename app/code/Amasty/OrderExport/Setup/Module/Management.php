<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Setup\Module;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Module\ModuleResource;

class Management
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var ModuleResource
     */
    private $moduleResource;

    public function __construct(
        ModuleManager $moduleManager,
        ModuleResource $moduleResource
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleResource = $moduleResource;
    }

    /**
     * Assert that specified module is disabled
     *
     * @param string $moduleName
     * @return void
     * @throws \RuntimeException
     */
    public function assertIsDisabled($moduleName)
    {
        if ($this->moduleManager->isEnabled($moduleName)) {
            $message = "\nThe installed '$moduleName' module is incompatible with 'Amasty_OrderExport'.\n\n"
                . "Please disable '$moduleName' module before running setup:upgrade using the command:\n\n"
                . " bin/magento module:disable $moduleName\n";

            throw new \RuntimeException($message);
        }
    }

    /**
     * Delete Db version of the module.
     * That means removing a row from 'setup_module' table
     *
     * @param string $moduleName
     * @return void
     * @throws LocalizedException
     */
    public function deleteDbVersion($moduleName)
    {
        if ($this->moduleResource->getDbVersion($moduleName) !== false) {
            $this->moduleResource->getConnection()
                ->delete($this->moduleResource->getMainTable(), ['module = ?' => $moduleName]);
        }
    }
}
