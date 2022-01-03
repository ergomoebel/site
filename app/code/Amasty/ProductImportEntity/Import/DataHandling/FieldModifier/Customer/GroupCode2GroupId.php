<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\FieldModifier\Customer;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\Customer\Attribute\Source;
use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;

class GroupCode2GroupId implements FieldModifierInterface
{
    /**
     * @var Source\Group
     */
    private $groupSource;

    private $map;

    public function __construct(Source\Group $groupSource)
    {
        $this->groupSource = $groupSource;
    }

    public function transform($value)
    {
        if (!is_array($value)) {
            $map = $this->getMap();
            return $map[$value] ?? $value;
        }

        return $value;
    }

    private function getMap(): array
    {
        if ($this->map === null) {
            foreach ($this->groupSource->getAllOptions() as $groupOption) {
                $map[$groupOption['label']] = $groupOption['value'];
            }

            $map['ALL GROUPS'] = (string)GroupInterface::CUST_GROUP_ALL;
            $map['NOT LOGGED IN'] = '0';
            $this->map = $map;
        }

        return $this->map;
    }
}
