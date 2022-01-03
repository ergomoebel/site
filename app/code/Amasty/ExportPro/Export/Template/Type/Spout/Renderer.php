<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Spout;

use Amasty\ExportCore\Api\ChunkStorageInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\Template\RendererInterface;
use Amasty\ExportCore\Export\Template\Type\Csv\Utils;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Amasty\ExportPro\Export\Template\Type\Ods\ConfigInterface as OdsConfigInterface;
use Amasty\ExportPro\Export\Template\Type\Xlsx\ConfigInterface as XlsxConfigInterface;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterFactory;
use Box\Spout\Writer\WriterInterface;

abstract class Renderer implements RendererInterface
{
    const EXTENSION = null;
    const WRITER_TYPE = \Box\Spout\Common\Type::CSV;

    /**
     * @var TmpFileManagement
     */
    private $tmp;

    /**
     * @var Utils\ConvertRowTo2DimensionalArray
     */
    private $convertRowTo2DimensionalArray;

    /**
     * @var Utils\ConvertRowToMergedList
     */
    private $convertRowToMergedList;

    /**
     * @var \Amasty\ExportCore\Export\Template\Type\Csv\Renderer
     */
    private $csvRenderer;

    public function __construct(
        TmpFileManagement $tmp,
        Utils\ConvertRowTo2DimensionalArray $convertRowTo2DimensionalArray,
        Utils\ConvertRowToMergedList $convertRowToMergedList,
        \Amasty\ExportCore\Export\Template\Type\Csv\Renderer $csvRenderer
    ) {
        $this->tmp = $tmp;
        $this->convertRowTo2DimensionalArray = $convertRowTo2DimensionalArray;
        $this->convertRowToMergedList = $convertRowToMergedList;
        $this->csvRenderer = $csvRenderer;
    }

    /**
     * @param ExportProcessInterface $exportProcess
     * @return OdsConfigInterface|XlsxConfigInterface
     */
    abstract protected function getConfig(ExportProcessInterface $exportProcess);

    public function render(
        ExportProcessInterface $exportProcess,
        ChunkStorageInterface $chunkStorage
    ) : RendererInterface {
        $chunkIds = $chunkStorage->getAllChunkIds();
        sort($chunkIds, SORT_NUMERIC);
        $resultTempFile = $this->tmp->getTempDirectory($exportProcess->getIdentity())->getAbsolutePath(
            $this->tmp->getResultTempFileName($exportProcess->getIdentity())
        );

        $writer = WriterFactory::createFromType(static::WRITER_TYPE);
        $writer->openToFile($resultTempFile);

        $headerRendered = false;
        $config = $this->getConfig($exportProcess);

        foreach ($chunkIds as $chunkId) {
            if (!($data = $chunkStorage->readChunk($chunkId))) {
                continue;
            }

            if (!$headerRendered && $config->isHasHeaderRow()) {
                $header = $this->csvRenderer->getHeader(
                    $exportProcess->getExtensionAttributes()->getHelper()->getHeaderStructure(),
                    $exportProcess->getProfileConfig()->getFieldsConfig()->getMap() ?? '',
                    $config->getPostfix()
                );
                $writer->addRows([WriterEntityFactory::createRowFromArray($header)]);
                $headerRendered = true;
            }
            $this->writeChunk($exportProcess, $writer, $data);
            $chunkStorage->deleteChunk($chunkId);
        }

        $writer->close();

        return $this;
    }

    public function getFileExtension(ExportProcessInterface $exportProcess): ?string
    {
        return static::EXTENSION;
    }

    protected function writeChunk(ExportProcessInterface $exportProcess, WriterInterface $writer, array $data)
    {
        $rows = [];
        $headerStructure = $exportProcess->getExtensionAttributes()->getHelper()->getHeaderStructure();

        foreach ($data as $row) {
            if ($this->getConfig($exportProcess)->isCombineChildRows()) {
                $convertedRow = $this->convertRowToMergedList->convert(
                    $row,
                    $headerStructure,
                    $this->getConfig($exportProcess)->getChildRowSeparator()
                );
                $rows[] = WriterEntityFactory::createRowFromArray(reset($convertedRow));
            } else {
                $convertedRowMatrix = $this->convertRowTo2DimensionalArray->convert($row, $headerStructure);
                foreach ($convertedRowMatrix as $convertedRow) {
                    $rows[] = WriterEntityFactory::createRowFromArray($convertedRow);
                }
            }
        }

        $writer->addRows($rows);
    }
}
