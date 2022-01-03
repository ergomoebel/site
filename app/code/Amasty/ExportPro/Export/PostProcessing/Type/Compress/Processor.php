<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\PostProcessing\Type\Compress;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\PostProcessing\ProcessorInterface;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Magento\Framework\Archive\Helper\File\Gz;
use Magento\Framework\Archive\Helper\File\GzFactory;

class Processor implements ProcessorInterface
{
    const TMP_FILE_SUFFIX = '.gz.tmp';
    const BUFFER_SIZE = 1024 * 1024 * 20;

    /**
     * @var TmpFileManagement
     */
    private $tmp;

    /**
     * @var GzFactory
     */
    private $gzFactory;

    public function __construct(
        GzFactory $gzFactory,
        TmpFileManagement $tmp
    ) {
        $this->tmp = $tmp;
        $this->gzFactory = $gzFactory;
    }

    public function process(ExportProcessInterface $exportProcess): ProcessorInterface
    {
        $exportProcess->addInfoMessage('Started compressing the file.');
        $tempDirectory = $this->tmp->getTempDirectory($exportProcess->getIdentity());
        $tmpFileName = $this->tmp->getResultTempFileName($exportProcess->getIdentity());
        $absoluteFilePath = $tempDirectory->getAbsolutePath($tmpFileName);

        $tmpFile = $tempDirectory->openFile($tmpFileName, 'r');
        /** @var Gz $gz */
        $gz = $this->gzFactory->create(['filePath' => $absoluteFilePath . self::TMP_FILE_SUFFIX]);
        $gz->open('wb');
        while (!$tmpFile->eof()) {
            $gz->write($tmpFile->read(self::BUFFER_SIZE));
        }
        $gz->close();
        $tmpFile->close();

        $tempDirectory->renameFile($tmpFileName . self::TMP_FILE_SUFFIX, $tmpFileName);
        $exportProcess->getExportResult()->setExtension(
            $exportProcess->getExportResult()->getExtension() . '.gz'
        );

        return $this;
    }
}
