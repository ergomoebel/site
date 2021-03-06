<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportExport
 */

declare(strict_types=1);

namespace Amasty\ImportExport\Setup;

use Magento\Framework\Module\Status;
use Magento\Framework\Module\Manager;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    const MODULES_TO_DISABLE = [
        'Amasty_ImportExportLite',
        'Amasty_ImportExportPro'
    ];

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Status
     */
    private $moduleStatus;

    public function __construct(
        Manager $moduleManager,
        Status $moduleStatus
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleStatus = $moduleStatus;
    }

    /**
     * @inheritDoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach (self::MODULES_TO_DISABLE as $moduleName) {
            if ($this->moduleManager->isEnabled($moduleName)) {
                $this->moduleStatus->setIsEnabled(false, [$moduleName]);
            }
        }
    }
}
