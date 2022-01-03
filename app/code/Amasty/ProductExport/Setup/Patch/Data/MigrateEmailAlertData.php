<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


declare(strict_types=1);

namespace Amasty\ProductExport\Setup\Patch\Data;

use Amasty\Base\Model\Serializer;
use Amasty\ExportPro\Export\Notification\Type\Email\Config;
use Amasty\ProductExport\Api\Data\ProfileInterface;
use Amasty\ProductExport\Model\Profile\ResourceModel;
use Amasty\ProductExport\Model\Profile\ResourceModel\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class MigrateEmailAlertData implements DataPatchInterface
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var CollectionFactory
     */
    private $profileCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ResourceModel\Profile
     */
    private $profileResource;

    public function __construct(
        Serializer $serializer,
        CollectionFactory $profileCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        ResourceModel\Profile $profileResource
    ) {
        $this->serializer = $serializer;
        $this->profileCollectionFactory = $profileCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->profileResource = $profileResource;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        try {
            $profileCollection = $this->profileCollectionFactory->create();
            $notificationData = [
                'email_notification_config' => [
                    Config::EMAIL_ALERT_ENABLED => (string)$this->getConfig('admin_email/enable_notify'),
                    Config::EMAIL_SENDER => (string)$this->getConfig('admin_email/send_to'),
                    Config::EMAIL_TEMPLATE => (string)$this->getConfig('admin_email/template'),
                    Config::EMAIL_RECIPIENTS => explode(',', $this->getConfig('admin_email/recipients') ?? ''),
                ]
            ];

            /** @var ProfileInterface $profile */
            foreach ($profileCollection as $profile) {
                $config = $this->serializer->unserialize($profile->getSerializedConfig());
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $config['extension_attributes'] = array_merge($config['extension_attributes'], $notificationData);
                $profile->setSerializedConfig($this->serializer->serialize($config));
                $this->profileResource->save($profile);
            }
        } catch (\Throwable $e) {
            null;
        }
    }

    private function getConfig(string $path)
    {
        return $this->scopeConfig->getValue('amproductexport/' . $path);
    }
}
