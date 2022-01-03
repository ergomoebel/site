<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Api\Data;

interface LastExportedIdInterface
{
    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string $type
     *
     * @return \Amasty\ExportPro\Api\Data\LastExportedIdInterface
     */
    public function setType(string $type): LastExportedIdInterface;

    /**
     * @return int|null
     */
    public function getExternalId(): ?int;

    /**
     * @param int $externalId
     *
     * @return \Amasty\ExportPro\Api\Data\LastExportedIdInterface
     */
    public function setExternalId(int $externalId): LastExportedIdInterface;

    /**
     * @return int|null
     */
    public function getLastExportedId(): ?int;

    /**
     * @param int $lastExportedId
     *
     * @return \Amasty\ExportPro\Api\Data\LastExportedIdInterface
     */
    public function setLastExportedId(int $lastExportedId): LastExportedIdInterface;
}
