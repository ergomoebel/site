<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Api\Data;

interface ProfileEventInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int|null $id
     * @return int|null
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getProfileId(): int;

    /**
     * @param int $profileId
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileEventInterface
     */
    public function setProfileId(int $profileId): ProfileEventInterface;

    /**
     * @return int
     */
    public function getEventName(): int;

    /**
     * @param int $eventName
     *
     * @return \Amasty\OrderExport\Api\Data\ProfileEventInterface
     */
    public function setEventName(int $eventName): ProfileEventInterface;
}
