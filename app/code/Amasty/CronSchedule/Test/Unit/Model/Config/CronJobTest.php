<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CronSchedule
 */


namespace Amasty\CronSchedule\Test\Unit\Model\Config;

use Magento\Framework\DataObject;

/**
 * @covers \Amasty\CronSchedule\Model\Config\CronJob
 */
class CronJobTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Data provider for cron jobs
     * @return array
     */
    public function getConfigDataProvider(): array
    {
        return [
            'export' => [
                'export',
                '1',
                '1 1 1 1 1',
                new DataObject([
                    'name'     => 'amasty_cron_run_export_1',
                    'instance' => \Amasty\CronSchedule\Cron\RunJobs::class,
                    'method'   => 'amasty_cron_run_export_external_id_1',
                    'schedule' => '1 1 1 1 1'
                ])
            ],
            'import' => [
                'import',
                '2',
                '* * * * *',
                new DataObject([
                    'name'     => 'amasty_cron_run_import_2',
                    'instance' => \Amasty\CronSchedule\Cron\RunJobs::class,
                    'method'   => 'amasty_cron_run_import_external_id_2',
                    'schedule' => '* * * * *'
                ])
            ],
            'custom' => [
                'custom',
                '3',
                '* 1 1 * *',
                new DataObject([
                    'name'     => 'amasty_cron_run_custom_3',
                    'instance' => \Amasty\CronSchedule\Cron\RunJobs::class,
                    'method'   => 'amasty_cron_run_custom_external_id_3',
                    'schedule' => '* 1 1 * *'
                ])
            ]
        ];
    }

    /**
     * @param string $jobType
     * @param string $externalId
     * @param string $expression
     * @param DataObject $expectedResult
     * @dataProvider getConfigDataProvider
     */
    public function testGetConfig(string $jobType, string $externalId, string $expression, DataObject $expectedResult)
    {
        $cronJobConfig = new \Amasty\CronSchedule\Model\Config\CronJob();

        $this->assertEquals($expectedResult, $cronJobConfig->getConfig($jobType, $externalId, $expression));
    }

    /**
     * Data provider for method matching
     * @return array
     */
    public function methodsDataProvider(): array
    {
        return [
            'correct' => [
                'amasty_cron_run_export_external_id_1',
                ['amasty_cron_run_export_external_id_1', 'export', '1']
            ],
            'almost' => [
                'amasty_cron_run_export_external_id_',
                []
            ],
            'wrong' => [
                'ewrwefwefwefwefwef',
                []
            ]
        ];
    }

    /**
     * @param string $name
     * @param array $expectedResult
     * @dataProvider methodsDataProvider
     */
    public function testMatchMethods(string $name, array $expectedResult)
    {
        $cronJobConfig = new \Amasty\CronSchedule\Model\Config\CronJob();

        $this->assertSame($expectedResult, $cronJobConfig->matchMethods($name));
    }
}
