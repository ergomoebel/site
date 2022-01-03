<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Setup\Patch\Data;

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
            'Amasty Import Products: Error happened',
            'amproductimport_admin_email_alert_template',
            'amproductimport/admin_email/alert_template',
            Area::AREA_ADMINHTML
        ]);

        return $this;
    }
}
