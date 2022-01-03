<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */


declare(strict_types=1);

namespace Amasty\OrderImport\Setup\Patch\Data;

use Amasty\ImportPro\Setup\Model\EmailTemplateDeployer;
use Magento\Framework\App\Area;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class DeployEmailTemplate implements DataPatchInterface
{
    /**
     * @var EmailTemplateDeployer
     */
    private $emailTemplateDeployer;

    public function __construct(EmailTemplateDeployer $emailTemplateDeployer)
    {
        $this->emailTemplateDeployer = $emailTemplateDeployer;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply(): DeployEmailTemplate
    {
        $this->emailTemplateDeployer->execute([
            'Amasty Import Orders: Error happened',
            'amorderimport_admin_email_alert_template',
            'amorderimport/admin_email/alert_template',
            Area::AREA_ADMINHTML
        ]);

        return $this;
    }
}
