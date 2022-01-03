<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\DataHandling\FieldModifier;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Magento\Store\Model\StoreManagerInterface;

class WebsiteId2WebsiteCode extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array|null
     */
    private $map;

    public function __construct(StoreManagerInterface $storeManager, $config)
    {
        parent::__construct($config);
        $this->storeManager = $storeManager;
    }

    public function transform($value)
    {
        $map = $this->getMap();
        return $map[$value] ?? $value;
    }

    /**
     * Get website Id to website code map
     *
     * @return array
     */
    private function getMap()
    {
        if (!$this->map) {
            $this->map = [0 => __('All Websites')];
            foreach ($this->storeManager->getWebsites() as $website) {
                $this->map[$website->getId()] = $website->getCode();
            }
        }
        return $this->map;
    }

    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }

    public function getLabel(): string
    {
        return __('Website Id To Website Code')->getText();
    }
}
