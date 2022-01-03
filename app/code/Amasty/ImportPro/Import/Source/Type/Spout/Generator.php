<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\Source\Type\Spout;

use Amasty\ImportCore\Api\Source\SourceGeneratorInterface;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Amasty\ImportCore\Import\Source\Utils\ConvertRowToArray;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterFactory;
use Box\Spout\Writer\WriterInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Math\Random;

abstract class Generator implements SourceGeneratorInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var ConvertRowToArray
     */
    private $convertRowToArray;

    public function __construct(
        Filesystem $filesystem,
        Random $random,
        ConvertRowToArray $convertRowToArray
    ) {
        $this->filesystem = $filesystem;
        $this->random = $random;
        $this->convertRowToArray = $convertRowToArray;
    }

    abstract public function getExtension(): string;

    public function generate(array $data): string
    {
        $randomName = $this->random->getRandomString(TmpFileManagement::TEMP_FILE_NAME_LENGTH);
        $tmpDir = $this->filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $tmpDir->touch($randomName);

        $writer = WriterFactory::createFromType($this->getExtension());
        $writer->openToFile($tmpDir->getAbsolutePath($randomName));
        $writer->addRows([WriterEntityFactory::createRowFromArray(
            $this->convertRowToArray->getHeaderRow($data)
        )]);

        $this->writeRows($data, $writer);
        $writer->close();

        $content = $tmpDir->getDriver()->fileGetContents($tmpDir->getAbsolutePath($randomName));
        $tmpDir->delete($randomName);

        return $content;
    }

    protected function writeRows(array $data, WriterInterface $writer)
    {
        $rows = [];

        foreach ($data as $row) {
            $convertedRowMatrix = $this->convertRowToArray->convert($row);
            foreach ($convertedRowMatrix as $convertedRow) {
                $rows[] = WriterEntityFactory::createRowFromArray($convertedRow);
            }
        }

        $writer->addRows($rows);
    }
}
