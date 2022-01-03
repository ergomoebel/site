<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteImportEntity
 */


declare(strict_types=1);

namespace Amasty\UrlRewriteImportEntity\Import\DataHandling\Entity\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\Store;

class NamesPath2EntityId
{
    /**
     * Category path delimiter
     */
    const PATH_DELIMITER = '/';

    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var array
     */
    private $namesPathToEntityIdMap;

    public function __construct(CollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Converts category names path to entity Id
     *
     * @param string $namesPath
     * @return int|null
     */
    public function execute($namesPath)
    {
        $namesPath = $this->standardizeString($namesPath);
        $pathToEntityIdMap = $this->getNamesPathToEntityIdMap();

        return $pathToEntityIdMap[$namesPath] ?? null;
    }

    /**
     * Get names path to entity Id map
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getNamesPathToEntityIdMap()
    {
        if (!$this->namesPathToEntityIdMap) {
            /** @var Collection $collection */
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('url_path')
                ->setStoreId(Store::DEFAULT_STORE_ID);

            /** @var Category $category */
            foreach ($collection as $category) {
                $structure = explode(self::PATH_DELIMITER, $category->getPath());
                $pathSize = count($structure);

                if ($pathSize > 1) {
                    $path = [];
                    for ($i = 1; $i < $pathSize; $i++) {
                        /** @var Category $item */
                        $item = $collection->getItemById((int)$structure[$i]);
                        if (!$item) {
                            continue;
                        }
                        $name = $item->getName();
                        $path[] = $this->quotePathDelimiter($name);
                    }

                    $namesPath = $this->standardizeString(implode(self::PATH_DELIMITER, $path));
                    $this->namesPathToEntityIdMap[$namesPath] = $category->getId();
                }
            }
        }

        return $this->namesPathToEntityIdMap;
    }

    /**
     * Quoting path delimiter in string
     *
     * @param string $string
     * @return string
     */
    private function quotePathDelimiter($string)
    {
        return str_replace(self::PATH_DELIMITER, '\\' . self::PATH_DELIMITER, $string);
    }

    /**
     * Standardize a string
     *
     * @param string $string
     * @return string
     */
    private function standardizeString($string)
    {
        return mb_strtolower($string);
    }
}
