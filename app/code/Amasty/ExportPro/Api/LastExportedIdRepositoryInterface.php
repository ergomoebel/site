<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Api;

use Amasty\ExportPro\Api\Data\LastExportedIdInterface;

/**
 * @api
 */
interface LastExportedIdRepositoryInterface
{
    /**
     * Save
     *
     * @param LastExportedIdInterface $history
     *
     * @return void
     */
    public function save(LastExportedIdInterface $history);

    /**
     * Get by type and external id
     *
     * @param string $type
     * @param int $externalId
     *
     * @return LastExportedIdInterface
     */
    public function getByTypeAndExternalId(string $type, int $externalId);
}
