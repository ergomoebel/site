<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Filesystem;

use Amasty\ImportCore\Api\Action\FileUploaderInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\Source\SourceDataStructure;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Gallery;
use Magento\CatalogImportExport\Model\Import\Uploader;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Math\Random;
use Magento\MediaStorage\Helper\File\Storage;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;
use Magento\Store\Model\Store;

class FileUploader extends Uploader implements FileUploaderInterface
{
    const PRODUCT_ATTRIBUTE_ENTITY_CODE = 'catalog_product_entity_attribute';

    /**
     * @var array
     */
    private $uploadRows;

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var Gallery
     */
    private $gallery;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        Database $coreFileStorageDb,
        Storage $coreFileStorage,
        AdapterFactory $imageFactory,
        NotProtectedExtension $validator,
        Filesystem $filesystem,
        Filesystem\File\ReadFactory $readFactory,
        EntityConfigProvider $entityConfigProvider,
        ProductAttributeRepositoryInterface $attributeRepository,
        Gallery $gallery,
        MetadataPool $metadataPool,
        ProductRepositoryInterface $productRepository,
        $filePath = null,
        Random $random = null
    ) {
        parent::__construct(
            $coreFileStorageDb,
            $coreFileStorage,
            $imageFactory,
            $validator,
            $filesystem,
            $readFactory,
            $filePath,
            $random
        );
        $this->entityConfigProvider = $entityConfigProvider;
        $this->attributeRepository = $attributeRepository;
        $this->gallery = $gallery;
        $this->metadataPool = $metadataPool;
        $this->productRepository = $productRepository;
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        if (empty($this->uploadRows)) {
            return;
        }
        foreach ($importProcess->getData() as $productData) {
            $this->processProductData($importProcess, $productData);
        }
    }

    public function initialize(ImportProcessInterface $importProcess): void
    {
        parent::init();
        $this->setTmpDir($importProcess->getProfileConfig()->getImagesFileDirectory());
        $this->setDestDir($importProcess->getEntityConfig()->getFileUploaderConfig()->getStoragePath());
        $fieldsConfig = $this->entityConfigProvider->get(self::PRODUCT_ATTRIBUTE_ENTITY_CODE)->getFieldsConfig();
        foreach ($fieldsConfig->getFields() as $field) {
            if ($field->isFile()) {
                $this->uploadRows[] = $field->getName();
            }
        }
    }

    private function processProductData(ImportProcessInterface $importProcess, array $productData)
    {
        $productAttributesData =
            $productData[SourceDataStructure::SUB_ENTITIES_DATA_KEY][self::PRODUCT_ATTRIBUTE_ENTITY_CODE] ?? [];
        if (empty($productAttributesData) || empty($productData['sku'])) {
            return;
        }
        $position = 0;
        foreach ($productAttributesData as $attributesData) {
            foreach ($this->uploadRows as $uploadRow) {
                if (!empty($attributesData[$uploadRow])) {
                    try {
                        $uploadedFile = parent::move($attributesData[$uploadRow], true);
                        $uploadedFileName = $uploadedFile['file'] ?? '';
                        if ($this->gallery->countImageUses($uploadedFileName) > 0) {
                            continue;
                        }

                        $storeId = $attributesData['store_id'] ?? Store::DEFAULT_STORE_ID;
                        $productSku = $productData['sku'];
                        $productId = $this->productRepository->get($productSku)->getId();
                        $galleryData = [
                            'attribute_id' => $this->getMediaGalleryAttributeId(),
                            'store_id' => $storeId,
                            'value' => $uploadedFileName,
                            $this->getProductEntityLinkField() => $productId
                        ];
                        $lastId = $this->gallery->insertGallery($galleryData);

                        $galleryValueInStoreData = [
                            'value_id' => $lastId,
                            'label' => $attributesData[$uploadRow . '_label'] ?? '',
                            'position' => ++$position,
                            'disabled' => '0',
                            $this->getProductEntityLinkField() => $productId
                        ];
                        $this->gallery->insertGalleryValueInStore($galleryValueInStoreData);

                        $this->gallery->bindValueToEntity($lastId, $productId);
                    } catch (LocalizedException $e) {
                        $importProcess->addErrorMessage($e->getMessage());
                    }
                }
            }
        }
    }

    private function getMediaGalleryAttributeId(): int
    {
        return (int)$this->attributeRepository->get('media_gallery')->getAttributeId();
    }

    private function getProductEntityLinkField(): string
    {
        return $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }
}
