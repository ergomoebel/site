<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\GoogleDrive\Utils;

use Amasty\Base\Model\Serializer;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Math\Random;
use Magento\MediaStorage\Model\File\UploaderFactory;

class KeyFileUploader
{
    const DRIVE_KEYS_PATH = 'google_drive';

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var WriteInterface
     */
    private $directory;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        WriteFactory $writeFactory,
        Random $random,
        Serializer $serializer
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->directory = $writeFactory->create(
            $filesystem->getDirectoryWrite(DirectoryList::TMP)
                ->getAbsolutePath(TmpFileManagement::IMPORT_FOLDER)
        );
        $this->random = $random;
        $this->serializer = $serializer;
    }

    public function uploadFile(string $fileId = 'key_file'): array
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        do {
            $randomName = $this->random->getUniqueHash() . '.' . $uploader->getFileExtension();
        } while ($this->directory->isExist(self::DRIVE_KEYS_PATH . DIRECTORY_SEPARATOR . $randomName));

        $uploader->setAllowedExtensions(['json']);
        $result = $uploader->save($this->directory->getAbsolutePath(self::DRIVE_KEYS_PATH), $randomName);
        if (!$result) {
            throw new LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }
        $result['name'] = $randomName;
        unset($result['path']);

        return $result;
    }

    public function getConfigFromFile(string $fileHash): array
    {
        $path = self::DRIVE_KEYS_PATH . DIRECTORY_SEPARATOR . $fileHash;

        if ($this->directory->isExist($path)) {
            return $this->serializer->unserialize(
                $this->directory->readFile($path)
            );
        }

        return [];
    }

    public function deleteFile(string $fileHash): void
    {
        $path = self::DRIVE_KEYS_PATH . DIRECTORY_SEPARATOR . $fileHash;

        if ($this->directory->isExist($path)) {
            $this->directory->delete($path);
        }
    }
}
