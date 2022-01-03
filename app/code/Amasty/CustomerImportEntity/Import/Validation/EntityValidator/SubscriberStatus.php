<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImportEntity
 */

declare(strict_types=1);

namespace Amasty\CustomerImportEntity\Import\Validation\EntityValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Newsletter\Model\Subscriber;

class SubscriberStatus implements FieldValidatorInterface
{
    private $subscriberStatuses = [
        Subscriber::STATUS_SUBSCRIBED,
        Subscriber::STATUS_NOT_ACTIVE,
        Subscriber::STATUS_UNSUBSCRIBED,
        Subscriber::STATUS_UNCONFIRMED
    ];

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            return in_array($row[$field], $this->subscriberStatuses);
        }

        return true;
    }
}
