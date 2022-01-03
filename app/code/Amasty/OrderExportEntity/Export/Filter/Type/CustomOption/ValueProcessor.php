<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Export\Filter\Type\CustomOption;

/**
 * Custom option filter processor
 */
class ValueProcessor
{
    /**
     * @var ValueItemInterfaceFactory
     */
    private $valueItemFactory;

    public function __construct(ValueItemInterfaceFactory $valueItemFactory)
    {
        $this->valueItemFactory = $valueItemFactory;
    }

    /**
     * Get value items instances from input raw string value
     *
     * @param string $rawValue
     * @return ValueItemInterface[]
     */
    public function getValueItems($rawValue)
    {
        $valueItems = [];
        foreach ($this->getKeyValuePairs($rawValue) as $keyValuePair) {
            /** @var ValueItemInterface $valueItem */
            $valueItem = $this->valueItemFactory->create();
            $valueItem->setKey($keyValuePair['key'])
                ->setValue($keyValuePair['value']);

            $valueItems[] = $valueItem;
        }
        return $valueItems;
    }

    /**
     * Get key value pair from input raw string value
     *
     * @param string $rawValue
     * @return array
     */
    private function getKeyValuePairs($rawValue)
    {
        $result = [];
        $indexKeyMap = [
            0 => 'key',
            1 => 'value'
        ];

        $data = array_map('trim', explode("\n", $rawValue));

        /**
         * Value data filter callback
         *
         * @param string $item
         * @return bool
         */
        $filterCallback = function ($item) {
            return !empty($item);
        };
        $data = array_filter($data, $filterCallback);

        $pairs = array_chunk($data, 2);
        foreach ($pairs as $pair) {
            $mappedPair = [];
            foreach ($indexKeyMap as $index => $key) {
                if (isset($pair[$index])) {
                    $mappedPair[$key] = $pair[$index];
                }
            }

            if (count($mappedPair) == 2) {
                $result[] = $mappedPair;
            }
        }

        return $result;
    }
}
