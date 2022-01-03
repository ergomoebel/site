<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\DataHandling\FieldModifier\Customer;

use Amasty\ProductExportEntity\Export\DataHandling\FieldModifier\SourceOption\Value2Label;
use Amasty\ExportCore\Export\SourceOption\CustomerGroupOptions;

class GroupId2GroupCode extends Value2Label
{
    /**
     * @var CustomerGroupOptions
     */
    private $sourceModel;

    public function __construct($config, CustomerGroupOptions $sourceModel)
    {
        parent::__construct($config);
        $this->sourceModel = $sourceModel;
    }

    protected function getSourceModel()
    {
        return $this->sourceModel;
    }

    public function getLabel(): string
    {
        return __('Convert Group Id To Group Code')->getText();
    }
}
