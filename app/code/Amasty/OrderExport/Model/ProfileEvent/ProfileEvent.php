<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\ProfileEvent;

use Amasty\OrderExport\Api\Data\ProfileEventInterface;
use Magento\Framework\Model\AbstractModel;

class ProfileEvent extends AbstractModel implements ProfileEventInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const PROFILE_ID = 'profile_id';
    const EVENT_NAME = 'event_name';
    /**#@-*/

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\ProfileEvent::class);
        $this->setIdFieldName(self::ID);
    }

    public function getProfileId(): int
    {
        return (int)$this->getData(self::PROFILE_ID);
    }

    public function setProfileId(int $profileId): ProfileEventInterface
    {
        $this->setData(self::PROFILE_ID, $profileId);

        return $this;
    }

    public function getEventName(): int
    {
        return (int)$this->getData(self::EVENT_NAME);
    }

    public function setEventName(int $eventName): ProfileEventInterface
    {
        $this->setData(self::EVENT_NAME, $eventName);

        return $this;
    }
}
