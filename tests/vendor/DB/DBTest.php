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
    protected static array $_dbTables = ['migrations'];

    /**
     * Test iceCMS2\DB
     *
     * @return void
     */
    public function testConnect(): void
    {
        $this->assertEquals(true, static::$_DB->getConnected());
    }

    /**
     * Test SetUpBeforeClass
     *
     * @return void
     */
    public function testSetUpBeforeClass(): void
    {
        $res = static::$_DB->query('SHOW CREATE TABLE `migrations`;');
        $this->assertEquals('migrations', $res[0]['Table']);

        $res = static::$_DB->query('SELECT * FROM `migrations`;');
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

        $res = static::$_DB->queryBinded('SELECT * FROM `migrations` WHERE `version` = ?', [
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

        static::$_DB->queryBinded(
            'INSERT INTO `migrations`(`version`, `name`, `start_time`, `end_time`) 
' . "VALUES(?, ?, ?, ?);",
            [
                0 => 20220623130057,
                1 => 'Icecms2CreateFilesTable',
                3 => '2022-07-04 21:11:50',
                4 => '2022-07-04 21:11:50',
            ]
        );

        $res = static::$_DB->queryBinded('SELECT * FROM `migrations` WHERE `version` = ?', [
            0 => 20220623130057
        ]);

        $this->assertEquals([
            'version' => 20220623130057,
            'name' => 'Icecms2CreateFilesTable',
            'start_time' => '2022-07-04 21:11:50',
            'end_time' => '2022-07-04 21:11:50',
            ], $res[0]);
    }
}