<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Test\Integration;

use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\ImportResultInterface;
use Amasty\ImportCore\Import\ImportProcess;
use Amasty\ImportPro\Model\History\History as HistoryModel;
use Amasty\ImportPro\Model\History\ResourceModel\History;
use Amasty\ImportPro\Model\OptionSource\HistoryStatus;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

class CreateImportHistoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->cleanupAll();
    }

    protected function tearDown(): void
    {
        $this->cleanupAll();
    }

    protected function cleanupAll()
    {
        /** @var ResourceConnection $connection */
        $connection = $this->objectManager->get(ResourceConnection::class);
        $tableName = $connection->getTableName(History::TABLE_NAME);
        $connection->getConnection()->truncateTable($tableName);
    }

    protected function getHistoryData(string $tableName, $processIdentity): array
    {
        /** @var ResourceConnection $connection */
        $connection = $this->objectManager->get(ResourceConnection::class);

        $historyData = $connection->getConnection()->fetchRow(
            $connection->getConnection()->select()->from($connection->getTableName($tableName))->where(
                HistoryModel::IDENTITY . ' = ?',
                $processIdentity
            )
        );

        if ($historyData) {
            unset($historyData[HistoryModel::LOG]);
            return $historyData;
        }

        return [];
    }

    /**
     * Data provider for testCreateHistory
     * @return array
     */
    public function importRunDataProvider(): array
    {
        return [
            [
                'faq_category',
                'faq_category_import',
                '7f6228ce-c698-46c2-abfe-111111111111',
                'import',
                [
                    'recordsAdded' => 4,
                    'totalRecords' => 12,
                    'messages' => 'Something went wrong'
                ],
                [
                    HistoryModel::HISTORY_ID => '1',
                    HistoryModel::ENTITY_CODE => 'faq_category',
                    HistoryModel::TYPE => 'faq_category_import',
                    HistoryModel::IDENTITY => '7f6228ce-c698-46c2-abfe-111111111111',
                    HistoryModel::STATUS => HistoryStatus::SUCCESS,
                    HistoryModel::JOB_ID => null,
                    HistoryModel::NAME => null
                ]
            ],
            [
                'faq_tags',
                'faq_tags_import',
                '7f6228ce-c698-46c2-abfe-222222222222',
                'validate_and_import',
                [
                    'recordsAdded' => 6,
                    'totalRecords' => 6,
                    'messages' => ''
                ],
                [
                    HistoryModel::HISTORY_ID => '1',
                    HistoryModel::ENTITY_CODE => 'faq_tags',
                    HistoryModel::TYPE => 'faq_tags_import',
                    HistoryModel::IDENTITY => '7f6228ce-c698-46c2-abfe-222222222222',
                    HistoryModel::STATUS => HistoryStatus::SUCCESS,
                    HistoryModel::JOB_ID => null,
                    HistoryModel::NAME => null
                ]
            ],
            [
                'faq_question_',
                'faq_question_import',
                '7f6228ce-c698-46c2-abfe-333333333333',
                'some_not_loggable_strategy',
                [
                    'recordsAdded' => 12,
                    'totalRecords' => 12,
                    'messages' => ''
                ],
                []
            ]
        ];
    }

    /**
     * @magentoDbIsolation disabled
     * @dataProvider importRunDataProvider
     *
     * @param string $entityCode
     * @param string $moduleType
     * @param string $processIdentity
     * @param string $strategy
     * @param array $result
     * @param array $expectedResult
     * @throws \Exception
     */
    public function testCreateHistory(
        string $entityCode,
        string $moduleType,
        string $processIdentity,
        string $strategy,
        array $result,
        array $expectedResult
    ) {
        $this->objectManager = Bootstrap::getObjectManager();
        /** @var ProfileConfigInterface $profileConfig */
        $profileConfig = $this->objectManager->create(ProfileConfigInterface::class);
        $profileConfig->setEntityCode($entityCode);
        $profileConfig->setModuleType($moduleType);
        $profileConfig->setStrategy($strategy);

        $importProcess = $this->objectManager->create(
            ImportProcess::class,
            [
                'identity' => $processIdentity,
                'profileConfig' => $profileConfig
            ]
        );

        /** @var ManagerInterface $eventManager */
        $eventManager = $this->objectManager->get(ManagerInterface::class);
        $eventManager->dispatch(
            'amimport_before_run',
            [
                'processIdentity' => $processIdentity,
                'importProcess' => $importProcess
            ]
        );

        /** @var ImportResultInterface $importResult */
        $importResult = $this->objectManager->create(ImportResultInterface::class);
        $importResult->setRecordsAdded($result['recordsAdded']);
        $importResult->logMessage(ImportResultInterface::MESSAGE_ERROR, $result['messages']);
        $importResult->setTotalRecords($result['totalRecords']);

        $eventManager->dispatch(
            'amimport_after_run',
            [
                'processIdentity' => $processIdentity,
                'importResult' => $importResult,
                'importProcess' => $importProcess
            ]
        );

        $historyData = $this->getHistoryData(History::TABLE_NAME, $processIdentity);

        if (!empty($expectedResult)) {
            $now = new \DateTime('now', new \DateTimeZone('utc'));
            foreach ([HistoryModel::STARTED_AT, HistoryModel::FINISHED_AT] as $timeField) {
                $this->assertEquals(
                    $now->getTimestamp(),
                    strtotime($historyData[$timeField]),
                    '',
                    5 // Compare two timestamps with some allowed discrepancy
                );
                unset($historyData[$timeField]);
            }
        }

        $this->assertEquals($expectedResult, $historyData);
    }
}
