<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Observer;

use Amasty\OrderExport\Model\OptionSource\ExportEvents;
use Amasty\OrderExport\Model\Profile\ProfileRunner;
use Amasty\OrderExport\Model\ProfileEvent\ProfileEvent as ProfileEventModel;
use Amasty\OrderExport\Model\ProfileEvent\ResourceModel\CollectionFactory;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class RunExportProfile implements ObserverInterface
{
    /**
     * @var ProfileRunner
     */
    private $profileRunner;

    /**
     * @var ExportEvents
     */
    private $exportEvents;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        ProfileRunner $profileRunner,
        ExportEvents $exportEvents,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->profileRunner = $profileRunner;
        $this->exportEvents = $exportEvents;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $eventName = $observer->getEvent()->getName();
            $eventId = $this->exportEvents->getEventIdByName($eventName);
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter(ProfileEventModel::EVENT_NAME, $eventId);

            foreach ($collection->getData() as $profile) {
                $this->profileRunner->run((int)$profile[ProfileEventModel::PROFILE_ID]);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
