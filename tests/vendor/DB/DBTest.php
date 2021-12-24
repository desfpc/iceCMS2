<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * DB Classes Tests TODO make framework class, extends PHPUnit\Framework\TestCase, with Settings and DB initialization
 */

namespace vendor\DB;

use iceCMS2\DB\DBFactory;
use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;
use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
    /**
     * DB Tables used for testing
     */
    private const DB_TABLES = ['migrations'];

    /**
     * @var Settings App settings
     */
    private static Settings $_settings;

    /**
     * @var DBInterface|null test DB instance
     */
    private static ?DBInterface $_DB;

    /**
     * @var DBInterface|null DB instance (for creating data in test DB instance)
     */
    private static ?DBInterface $_realDB;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        $dir = str_replace('tests' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'DB', '', __DIR__);
        require_once $dir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'settingsSelector.php';
        /** @var array $settings */
        self::$_settings = new Settings($settings);
        self::$_realDB = (new DBFactory(self::$_settings))->DB;
        self::$_settings->db = self::$_settings->db_test;
        self::$_DB = (new DBFactory(self::$_settings))->DB;

        //Copy Tables structure from real DB to test DB
        if (!empty(self::DB_TABLES)) {
            self::$_realDB->connect();
            self::$_DB->connect();
            foreach (self::DB_TABLES as $table) {
                if ($createTableSQL = self::$_realDB->query('SHOW CREATE TABLE `' . $table . '`;')) {
                    $createTableSQL = $createTableSQL[0]['Create Table'];
                    if (self::$_DB->query($createTableSQL)) {
                        echo "\nTest table " . $table . ' created';
                    }
                }
                //Insert test Data - find self::DB_TABLES json file for insert
                if ($data = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $table . '.json')) {
                    if ($data = json_decode($data, true)) {
                        foreach ($data as $row) {
                            $values = '';
                            $binded = [];
                            $query = 'INSERT INTO `' . $table . '`(';
                            $i = 0;
                            foreach ($row as $key => $value) {
                                ++$i;
                                if ($i > 1) {
                                    $query .= ', ';
                                }
                                $query .= '`' . $key . '`';
                                if ($values !== '') {
                                    $values .= ', ';
                                }
                                $values .= '?';
                                $binded[':' . $key] = $value;
                            }
                            $query .= ') VALUES(' . $values . ')';
                            self::$_DB->queryBinded($query, $binded);
                        }
                    }
                }
            }
            self::$_realDB->disconnect();
        }
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        if (!empty(self::DB_TABLES)) {
            foreach (self::DB_TABLES as $table) {
                self::$_DB->query('DROP TABLE IF EXISTS `' . $table . '`');
            }
        }
    }

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