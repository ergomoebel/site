<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Setup\Model;

use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\State;
use Magento\Store\Model\Store;

class EmailTemplateDeployer
{
    /**
     * @var TemplateFactory
     */
    private $emailTemplateFactory;

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
        TemplateFactory $emailTemplateFactory,
        WriterInterface $configWriter,
        State $appState,
        TypeListInterface $typeList
    ) {
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->configWriter = $configWriter;
        $this->appState = $appState;
        $this->typeList = $typeList;
    }

    public function execute(array $templateData): void
    {
        $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($templateData) {
            $this->deployEmailTemplate(...$templateData);
        });
        $this->typeList->invalidate(Config::TYPE_IDENTIFIER);
    }

    private function deployEmailTemplate(
        string $code,
        string $originalCode,
        string $fullConfigPath,
        $area = Area::AREA_FRONTEND
    ) {
        try {
            /** @var \Magento\Email\Model\Template $mailTemplate */
            $mailTemplate = $this->emailTemplateFactory->create();
            $mailTemplate->setDesignConfig(['area' => $area, 'store' => Store::DEFAULT_STORE_ID]);
            $mailTemplate->loadDefault($originalCode);
            $mailTemplate->setTemplateCode($code);
            $mailTemplate->setOrigTemplateCode($originalCode);
            $mailTemplate->setId(null);
            $mailTemplate->save();
            $this->configWriter->save($fullConfigPath, $mailTemplate->getId());
        } catch (\Exception $e) {
            null;
        }
    }
}
