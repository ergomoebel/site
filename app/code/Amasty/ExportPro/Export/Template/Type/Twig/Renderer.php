<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Twig;

use Amasty\ExportCore\Api\ChunkStorageInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\Template\RendererInterface;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Amasty\ExportPro\Export\Template\Type\Twig\Sandbox\Policy;

class Renderer implements RendererInterface
{
    const TYPE_ID = 'twig';

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var TmpFileManagement
     */
    private $tmp;

    /**
     * @var array
     */
    private $extensions;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var Policy
     */
    private $sandboxPolicy;

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        TmpFileManagement $tmp,
        Policy $sandboxPolicy,
        array $extensions = [],
        bool $debug = false
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->tmp = $tmp;
        $this->extensions = $extensions;
        $this->debug = $debug;
        $this->sandboxPolicy = $sandboxPolicy;
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

        $twigTemplateConfig = $exportProcess->getProfileConfig()->getExtensionAttributes()->getTwigTemplate();
        $loader = new \Twig\Loader\ArrayLoader([
            'content.twig' => $twigTemplateConfig->getContent(),
        ]);
        $twig = new \Twig\Environment($loader, ['debug' => $this->debug]);
        if ($this->debug) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }
        if ($this->sandboxPolicy->isEnabled()) {
            $policy = new \Twig\Sandbox\SecurityPolicy(
                $this->sandboxPolicy->getTags(),
                $this->sandboxPolicy->getFilters(),
                $this->sandboxPolicy->getMethods(),
                $this->sandboxPolicy->getProperties(),
                $this->sandboxPolicy->getFunctions()
            );
            $twig->addExtension((new \Twig\Extension\SandboxExtension($policy, true)));
        }
        if (!empty($this->extensions)) {
            foreach ($this->extensions as $extension) {
                $twig->addExtension($extension->create());
            }
        }

        $resultTempFile->write($this->renderHeader($exportProcess));
        $separator = $twigTemplateConfig->getSeparator();
        $firstItem = true;
        foreach ($chunkIds as $chunkId) {
            $data = $chunkStorage->readChunk($chunkId);
            if (!empty($data)) {
                foreach ($data as $row) {
                    $itemText = $twig->render('content.twig', ['item' => $row]);
                    if (!$firstItem) {
                        $itemText = $separator . $itemText;
                    } else {
                        $firstItem = false;
                    }
                    $resultTempFile->write($itemText);
                }
            }
            $chunkStorage->deleteChunk($chunkId);
        }
        $resultTempFile->write($this->renderFooter($exportProcess));

        $resultTempFile->close();

        return $this;
    }

    public function renderHeader(ExportProcessInterface $exportProcess): string
    {
        return $exportProcess->getProfileConfig()->getExtensionAttributes()->getTwigTemplate()->getHeader();
    }

    protected function renderFooter(ExportProcessInterface $exportProcess): string
    {
        return $exportProcess->getProfileConfig()->getExtensionAttributes()->getTwigTemplate()->getFooter();
    }

    public function getFileExtension(ExportProcessInterface $exportProcess): ?string
    {
        return $exportProcess->getProfileConfig()->getExtensionAttributes()->getTwigTemplate()->getExtension();
    }
}
