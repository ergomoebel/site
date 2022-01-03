<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\Source\Type\Xlsx;

use Amasty\ImportCore\Api\ImportProcessInterface;

class Reader extends \Amasty\ImportPro\Import\Source\Type\Spout\Reader
{
    const TYPE_ID = 'xlsx';

    public function estimateRecordsCount(): int
    {
        $rows = 0;
        $sheetIterator = $this->fileReader->getSheetIterator();
        $rowIterator = $sheetIterator->current()->getRowIterator();

        foreach ($rowIterator as $row) {
            if ($rowIterator->key() == 1) {
                continue; //skip header row for count of records
            }
            if ($row) {
                $rows++;
            }
        }
        $sheetIterator->rewind();
        $rowIterator->rewind();
        $this->readTypeMatchedRow(); //skip reading header row

        return $rows;
    }

    protected function getSourceConfig(ImportProcessInterface $importProcess)
    {
        return $importProcess->getProfileConfig()->getExtensionAttributes()->getXlsxSource();
    }

    protected function readTypeMatchedRow()
    {
        do {
            $rowIterator = $this->fileReader->getSheetIterator()->current()->getRowIterator();

            if ($rowIterator->current() === null) {
                $rowIterator->rewind();
            }
            $rowData = $rowIterator->current()->toArray();
            $rowIterator->next();

            if (empty($rowData)) {
                return false;
            }
            $rowData = $this->prepareRowData($rowData);
        } while ($this->isRowEmpty($rowData));

        return $rowData;
    }
}
