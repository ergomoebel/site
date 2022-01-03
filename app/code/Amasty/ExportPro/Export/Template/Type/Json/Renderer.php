<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Json;

use Amasty\ExportCore\Api\ChunkStorageInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\Template\RendererInterface;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;

class Renderer implements RendererInterface
{
    const TYPE_ID = 'json';

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var TmpFileManagement
     */
    private $tmp;

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        TmpFileManagement $tmp
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->tmp = $tmp;
    }

    public function render(
        ExportProcessInterface $exportProcess,
        ChunkStorageInterface $chunkStorage
    ) : RendererInterface {
        $chunkIds = $chunkStorage->getAllChunkIds();
        ksort($chunkIds, SORT_NUMERIC);
        $resultTempFile = $this->tmp->getTempDirectory($exportProcess->getIdentity())->openFile(
            $this->tmp->getResultTempFileName($exportProcess->getIdentity())
        );

        $resultTempFile->write($this->renderHeader($exportProcess));
        $rootMap = $exportProcess->getProfileConfig()->getFieldsConfig()->getMap();
        if (!empty($rootMap)) {
            $resultTempFile->write('"' . $rootMap . '": [');
        }
        $firstRow = true;
        foreach ($chunkIds as $chunkId) {
            foreach ($chunkStorage->readChunk($chunkId) as $row) {
                if (!$firstRow) {
                    $resultTempFile->write(',');
                }
                $firstRow = false;
                $resultTempFile->write(\json_encode($row));
            }
            $chunkStorage->deleteChunk($chunkId);
        }
        if (!empty($rootMap)) {
            $resultTempFile->write(']');
        }
        $resultTempFile->write($this->renderFooter($exportProcess));

        $resultTempFile->close();

        return $this;
    }

    public function getFileExtension(ExportProcessInterface $exportProcess): ?string
    {
        return 'json';
    }

    public function renderHeader(ExportProcessInterface $exportProcess): string
    {
        return $exportProcess->getProfileConfig()->getExtensionAttributes()->getJsonTemplate()->getHeader();
    }

    protected function renderFooter(ExportProcessInterface $exportProcess): string
    {
        return $exportProcess->getProfileConfig()->getExtensionAttributes()->getJsonTemplate()->getFooter();
    }
}
