<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\Source\Type\Spout;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Api\Source\SourceReaderInterface;
use Amasty\ImportCore\Import\FileResolver\FileResolverAdapter;
use Amasty\ImportCore\Import\Source\Data\DataStructureProvider;
use Amasty\ImportCore\Import\Source\Utils\FileRowToArrayConverter;
use Amasty\ImportCore\Import\Source\Utils\HeaderStructureProcessor;
use Amasty\ImportPro\Import\Source\Type\Ods\ConfigInterface as OdsConfig;
use Amasty\ImportPro\Import\Source\Type\Xlsx\ConfigInterface as XlsxConfig;
use Box\Spout\Reader\Common\Creator\ReaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

abstract class Reader implements SourceReaderInterface
{
    /**
     * @var \Box\Spout\Reader\ODS\Reader|\Box\Spout\Reader\XLSX\Reader
     */
    protected $fileReader;

    /**
     * @var OdsConfig|XlsxConfig
     */
    private $config;

    /**
     * @var DataStructureProvider
     */
    private $dataStructureProvider;

    /**
     * @var FileResolverAdapter
     */
    private $fileResolverAdapter;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var FileRowToArrayConverter
     */
    private $fileRowToArrayConverter;

    /**
     * @var HeaderStructureProcessor
     */
    private $headerStructureProcessor;

    /**
     * @var array
     */
    private $headerStructure;

    /**
     * @var array
     */
    private $nextRow = null;

    public function __construct(
        DataStructureProvider $dataStructureProvider,
        FileResolverAdapter $fileResolverAdapter,
        Filesystem $filesystem,
        FileRowToArrayConverter $fileRowToArrayConverter,
        HeaderStructureProcessor $headerStructureProcessor
    ) {
        $this->dataStructureProvider = $dataStructureProvider;
        $this->fileResolverAdapter = $fileResolverAdapter;
        $this->filesystem = $filesystem;
        $this->fileRowToArrayConverter = $fileRowToArrayConverter;
        $this->headerStructureProcessor = $headerStructureProcessor;
    }

    abstract protected function readTypeMatchedRow();

    abstract protected function getSourceConfig(ImportProcessInterface $importProcess);

    public function initialize(ImportProcessInterface $importProcess)
    {
        $fileName = $this->fileResolverAdapter->getFileResolver(
            $importProcess->getProfileConfig()->getFileResolverType()
        )->execute($importProcess);
        $filePath = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)
            ->getAbsolutePath($fileName);

        $this->fileReader = ReaderFactory::createFromType(static::TYPE_ID);
        $this->fileReader->open($filePath);
        $this->fileReader->getSheetIterator()->rewind();

        $this->config = $this->getSourceConfig($importProcess);
        $dataStructure = $this->dataStructureProvider->getDataStructure(
            $importProcess->getEntityConfig(),
            $importProcess->getProfileConfig()
        );
        $this->headerStructure = $this->headerStructureProcessor->getHeaderStructure(
            $dataStructure,
            $this->readTypeMatchedRow(),
            $this->config->getPrefix()
        );
    }

    public function readRow()
    {
        if ($this->nextRow === null) {
            $rowData = $this->readTypeMatchedRow();
        } else {
            $rowData = $this->nextRow;
        }

        if (!is_array($rowData)) {
            return false;
        }
        $rowData = $this->fileRowToArrayConverter->convertRowToHeaderStructure(
            $this->headerStructure,
            $rowData
        );

        if ($this->config->isCombineChildRows()) {
            $rowData = $this->fileRowToArrayConverter->formatMergedSubEntities(
                $rowData,
                $this->headerStructure,
                $this->config->getChildRowSeparator()
            );
        } else {
            $rowData = $this->checkAndMergeSubEntities($rowData);
        }

        return $rowData;
    }

    protected function checkAndMergeSubEntities(array $currentRow)
    {
        $this->nextRow = $this->readTypeMatchedRow();

        if (!$this->isNextRowValidForMergeProcessing()) {
            return $currentRow;
        }

        do {
            $nextRow = $this->fileRowToArrayConverter->convertRowToHeaderStructure(
                $this->headerStructure,
                $this->nextRow
            );

            $currentRow = $this->fileRowToArrayConverter->mergeRows($currentRow, $nextRow, $this->headerStructure);
            $this->nextRow = $this->readTypeMatchedRow();
        } while ($this->isNextRowValidForMergeProcessing());

        return $currentRow;
    }

    private function isNextRowValidForMergeProcessing(): bool
    {
        if (!is_array($this->nextRow)) {
            return false;
        }

        $row = $this->fileRowToArrayConverter->convertRowToHeaderStructure(
            $this->headerStructure,
            $this->nextRow
        );
        foreach ($row as $value) {
            if (!is_array($value) && !empty($value)) {
                return false;
            }
        }

        return true;
    }

    protected function prepareRowData(array $rowData): array
    {
        foreach ($this->headerStructureProcessor->getColNumbersToSkip() as $key) {
            unset($rowData[$key]);
        }
        ksort($rowData);

        return array_values($rowData);
    }

    protected function isRowEmpty(array $row): bool
    {
        return empty(array_filter($row));
    }
}
