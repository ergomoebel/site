<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\Profile;

use Amasty\CronSchedule\Api\ScheduleRepositoryInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportExportCore\Utils\Serializer;
use Amasty\OrderExport\Api\Data\ProfileInterface;
use Amasty\OrderExport\Api\Data\ProfileInterfaceFactory;
use Amasty\OrderExport\Api\ProfileRepositoryInterface;
use Amasty\OrderExport\Model\OptionSource\ExportEvents;
use Amasty\OrderExport\Model\ProfileEvent\ProfileEvent;
use Amasty\OrderExport\Model\ProfileEvent\ProfileEventFactory;
use Amasty\OrderExport\Model\ProfileEvent\ResourceModel\CollectionFactory;
use Amasty\OrderExport\Model\ProfileEvent\ResourceModel\ProfileEvent as ProfileEventResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository implements ProfileRepositoryInterface
{
    /**
     * @var ProfileInterfaceFactory
     */
    private $profileFactory;

    /**
     * @var ResourceModel\Profile
     */
    private $profileResource;

    /**
     * @var CollectionFactory
     */
    private $profileEventCollection;

    /**
     * @var ProfileEventResource
     */
    private $profileEventResource;

    /**
     * @var ProfileEventFactory
     */
    private $profileEventFactory;

    /**
     * @var ExportEvents
     */
    private $exportEvents;

    private $profiles = [];

    /**
     * @var ScheduleRepositoryInterface
     */
    private $scheduleRepository;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ProfileInterfaceFactory $profileFactory,
        ResourceModel\Profile $profileResource,
        ScheduleRepositoryInterface $scheduleRepository,
        CollectionFactory $profileEventCollectionFactory,
        ExportEvents $exportEvents,
        ProfileEventResource $profileEventResource,
        ProfileEventFactory $profileEventFactory,
        Serializer $serializer
    ) {
        $this->profileFactory = $profileFactory;
        $this->profileResource = $profileResource;
        $this->scheduleRepository = $scheduleRepository;
        $this->exportEvents = $exportEvents;
        $this->profileEventResource = $profileEventResource;
        $this->profileEventFactory = $profileEventFactory;
        $this->profileEventCollection = $profileEventCollectionFactory->create();
        $this->serializer = $serializer;
    }

    public function getById(int $id): ProfileInterface
    {
        if (!isset($this->profiles[$id])) {
            /** @var ProfileInterface $profile */
            $profile = $this->profileFactory->create();
            $this->profileResource->load($profile, $id);
            if (!$profile->getId()) {
                throw new NoSuchEntityException(__('Profile with specified ID "%1" not found.', $id));
            }

            $profileConfig = $this->serializer->unserialize(
                $profile->getSerializedConfig(),
                ProfileConfigInterface::class
            );
            $profile->setConfig($profileConfig);
            $profile->setProfileEvents($this->getProfileEventsId($profile->getId()));

            $this->profiles[$id] = $profile;
        }

        return $this->profiles[$id];
    }

    private function getProfileEventsId($profileId)
    {
        $profileEventIds = [];
        $collection = $this->profileEventCollection->addFieldToFilter(ProfileEvent::PROFILE_ID, $profileId);

        foreach ($collection->getData() as $item) {
            $profileEventIds[] = (string)$item[ProfileEvent::EVENT_NAME];
        }

        return $profileEventIds;
    }

    public function save(ProfileInterface $profile): ProfileInterface
    {
        try {
            $profile->getConfig()->initialize();
            $profile->setSerializedConfig(
                $this->serializer->serialize($profile->getConfig(), ProfileConfigInterface::class)
            );
            $this->profileResource->save($profile);

            if ($scheduleModel = $profile->getSchedule()) {
                $scheduleModel->setExternalId((int)$profile->getId());
                $this->scheduleRepository->save($scheduleModel);
            }

            $this->profileEventResource->deleteByProfileId((int)$profile->getId());

            if ($profileEvents = $profile->getProfileEvents()) {
                foreach ($profileEvents as $event) {
                    $profileEvent = $this->profileEventFactory->create();
                    $profileEvent->setProfileId((int)$profile->getId());
                    $profileEvent->setEventName((int)$event);
                    $this->profileEventResource->save($profileEvent);
                }
            }

            unset($this->profiles[$profile->getId()]);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save the profile. Error: %1', $e->getMessage()));
        }

        return $profile;
    }

    public function delete(ProfileInterface $profile): bool
    {
        try {
            $this->profileResource->delete($profile);
            unset($this->profiles[$profile->getId()]);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to delete the profile. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteById(int $id): bool
    {
        $this->delete($this->getById($id));

        return true;
    }

    public function updateLastExported(int $id): ProfileInterface
    {
        $profile = $this->getById($id);
        $now = new \DateTime('now', new \DateTimeZone('utc'));
        $profile->setExportedAt($now->format('Y-m-d H:i:s'));
        $this->profileResource->save($profile);

        return $profile;
    }
}
