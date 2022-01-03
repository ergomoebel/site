<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Model\Profile;

use Amasty\CronSchedule\Api\ScheduleRepositoryInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportExportCore\Utils\Serializer;
use Amasty\ProductImport\Api\Data\ProfileInterface;
use Amasty\ProductImport\Api\Data\ProfileInterfaceFactory;
use Amasty\ProductImport\Api\ProfileRepositoryInterface;
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
        Serializer $serializer
    ) {
        $this->profileFactory = $profileFactory;
        $this->profileResource = $profileResource;
        $this->scheduleRepository = $scheduleRepository;
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

            $this->profiles[$id] = $profile;
        }

        return $this->profiles[$id];
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

    public function updateLastImported(int $id): ProfileInterface
    {
        $profile = $this->getById($id);
        $now = new \DateTime('now', new \DateTimeZone('utc'));
        $profile->setImportedAt($now->format('Y-m-d H:i:s'));
        $this->profileResource->save($profile);

        return $profile;
    }
}
