<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


namespace Amasty\OrderExport\Setup;

use Amasty\OrderExport\Model\ConfigProvider;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\State;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var TemplateFactory
     */
    private $emailTemplate;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var TypeListInterface
     */
    private $typeList;

    public function __construct(
        TemplateFactory $emailTemplate,
        WriterInterface $configWriter,
        State $appState,
        TypeListInterface $typeList
    ) {
        $this->emailTemplate = $emailTemplate;
        $this->configWriter = $configWriter;
        $this->appState = $appState;
        $this->typeList = $typeList;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'saveAndSetEmails']);
    }

    /**
     * @return void
     */
    public function saveAndSetEmails()
    {
        $this->saveAndSetEmail(
            'Amasty Export Orders: Export failed',
            'amorderexport_admin_email_template',
            'amorderexport/admin_email/template',
            Area::AREA_ADMINHTML
        );
        $this->typeList->invalidate(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
    }

    /**
     * @param string $code
     * @param string $originalCode
     * @param string $configPath
     * @param string $area
     */
    private function saveAndSetEmail($code, $originalCode, $configPath, $area = Area::AREA_FRONTEND)
    {
        try {
            /** @var \Magento\Email\Model\Template $mailTemplate */
            $mailTemplate = $this->emailTemplate->create();

            $mailTemplate->setDesignConfig(
                ['area' => $area, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID]
            )->loadDefault(
                $originalCode
            )->setTemplateCode(
                $code
            )->setOrigTemplateCode(
                $originalCode
            )->setId(
                null
            )->save();

            $this->configWriter->save($configPath, $mailTemplate->getId());
        } catch (\Exception $e) {
            null;
        }
    }
}
