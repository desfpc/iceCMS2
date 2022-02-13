<?php
declare(strict_types=1);

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * DB Classes Tests
 */

namespace vendor\DB;

use iceCMS2\Tests\Ice2CMSTestCase;

class DBTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected const DB_TABLES = ['migrations'];

    /**
     * Test iceCMS2\DB
     *
     * @return void
     */
    public function testConnect(): void
    {
        $this->assertEquals(true, self::$_DB->getConnected());
    }

    /**
     * TestSetUpBeforeClass
     */
    public function testSetUpBeforeClass(): void
    {
        $res = self::$_DB->query('SHOW CREATE TABLE `migrations`;');
        $this->assertEquals('migrations', $res[0]['Table']);

        $res = self::$_DB->query('SELECT * FROM `migrations`;');
        $this->assertEquals([
            0 => [
                'version' => '20211217193000',
                'name' => 'TestMigration1',
                'start_time' => '2021-12-23 19:40:45',
                'end_time' => '2021-12-23 19:40:45',
            ],
            1 => [
                'version' => '20211217194500',
                'name' => 'TestMigration2',
                'start_time' => '2021-12-23 19:41:45',
                'end_time' => '2021-12-23 19:41:45',
            ],
        ], $res);

        $res = self::$_DB->queryBinded('SELECT * FROM `migrations` WHERE `version` = ?', [
            0 => 20211217193000
        ]);
        $this->assertEquals([
            0 => [
                'version' => '20211217193000',
                'name' => 'TestMigration1',
                'start_time' => '2021-12-23 19:40:45',
                'end_time' => '2021-12-23 19:40:45',
            ],
        ], $res);
    }
}