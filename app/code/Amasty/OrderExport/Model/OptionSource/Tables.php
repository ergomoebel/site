<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Model\OptionSource;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\OptionSourceInterface;

class Tables implements OptionSourceInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function toOptionArray()
    {
        $tables = [];

        foreach ($this->resource->getConnection()->getTables() as $table) {
            $tables[] = ['label' => $table, 'value' => $table];
        }

        return $tables;
    }
}
