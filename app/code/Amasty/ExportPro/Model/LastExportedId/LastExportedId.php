<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\LastExportedId;

use Amasty\ExportPro\Api\Data\LastExportedIdInterface;
use Magento\Framework\Model\AbstractModel;

class LastExportedId extends AbstractModel implements LastExportedIdInterface
{
    const ID = 'id';
    const TYPE = 'type';
    const EXTERNAL_ID = 'external_id';
    const LAST_EXPORTED_ID = 'last_exported_id';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\LastExportedId::class);
        $this->setIdFieldName(self::ID);
    }

    public function getType(): ?string
    {
        return $this->getData(self::TYPE);
    }

    public function setType(string $type): LastExportedIdInterface
    {
        $this->setData(self::TYPE, $type);

        return $this;
    }

    public function getExternalId(): ?int
    {
        return (int)$this->getData(self::EXTERNAL_ID);
    }

    public function setExternalId(int $externalId): LastExportedIdInterface
    {
        $this->setData(self::EXTERNAL_ID, (int)$externalId);

        return $this;
    }

    public function getLastExportedId(): ?int
    {
        return (int)$this->getData(self::LAST_EXPORTED_ID);
    }

    public function setLastExportedId(int $lastExportedId): LastExportedIdInterface
    {
        $this->setData(self::LAST_EXPORTED_ID, $lastExportedId);

        return $this;
    }
}
