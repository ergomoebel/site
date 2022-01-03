<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


declare(strict_types=1);

namespace Amasty\CronSchedule\Utils;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Framework\Webapi\ServiceOutputProcessor;

class ObjectConverter
{
    /**
     * @var ServiceInputProcessor
     */
    private $serviceInputProcessor;

    /**
     * @var ServiceOutputProcessor
     */
    private $serviceOutputProcessor;

    /**
     * @var Json
     */
    private $jsonSerializer;

    public function __construct(
        ServiceInputProcessor $serviceInputProcessor,
        ServiceOutputProcessor $serviceOutputProcessor,
        Json $jsonSerializer
    ) {
        $this->serviceInputProcessor = $serviceInputProcessor;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function serialize($object, string $type): string
    {
        return $this->jsonSerializer->serialize(
            $this->convertObjectToArray($object, $type)
        );
    }

    public function convertObjectToArray($object, string $type)
    {
        return $this->serviceOutputProcessor->convertValue($object, $type);
    }

    public function convertSerializedToArray(string $serialized)
    {
        return $this->jsonSerializer->unserialize($serialized);
    }

    public function unserialize(string $serialized, string $type)
    {
        return $this->serviceInputProcessor->convertValue(
            $this->convertSerializedToArray($serialized),
            $type
        );
    }
}
