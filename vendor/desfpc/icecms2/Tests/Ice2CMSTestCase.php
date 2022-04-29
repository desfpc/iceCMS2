<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * TestCase class - setup site settings and test DB (if const DB_TABLES not empty in test class)
 */

namespace iceCMS2\Tests;

use iceCMS2\DB\DBFactory;
use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;
use PHPUnit\Framework\TestCase;
use \ReflectionClass;

abstract class Ice2CMSTestCase extends TestCase
{
    /**
     * DB Tables used for testing
     */
    protected const DB_TABLES = [];

    /**
     * @var Settings App settings
     */
    protected static Settings $_settings;

    /**
     * @var DBInterface|null test DB instance
     */
    protected static ?DBInterface $_DB;

    /**
     * @var DBInterface|null DB instance (for creating data in test DB instance)
     */
    protected static ?DBInterface $_realDB;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        $dir = '';
        $dirArr = explode(DIRECTORY_SEPARATOR, __DIR__);
        foreach ($dirArr as $value) {
            if ($value === 'vendor') {
                break;
            }
            if ($dir !== '') {
                $dir .= DIRECTORY_SEPARATOR;
            }
            $dir .= $value;
        }

        require $dir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'settingsSelector.php';
        /** @var array $settings */
        static::$_settings = new Settings($settings);
        static::$_realDB = (new DBFactory(static::$_settings))->DB;
        static::$_settings->db = static::$_settings->db_test;
        static::$_DB = (new DBFactory(static::$_settings))->DB;

        //Copy Tables structure from real DB to test DB
        if (!empty(static::DB_TABLES)) {
            static::$_realDB->connect();
            static::$_DB->connect();
            foreach (static::DB_TABLES as $table) {
                if ($createTableSQL = static::$_realDB->query('SHOW CREATE TABLE `' . $table . '`;')) {
                    $createTableSQL = $createTableSQL[0]['Create Table'];
                    if (static::$_DB->query($createTableSQL)) {
                        echo "\nTest table " . $table . ' created';
                    }
                }
                //Insert test Data - find static::DB_TABLES json file for insert
                if ($data = file_get_contents(static::_getPath() . DIRECTORY_SEPARATOR . $table . '.json')) {
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
                            static::$_DB->queryBinded($query, $binded);
                        }
                    }
                }
            }
            static::$_realDB->disconnect();
        }
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        if (!empty(static::DB_TABLES)) {
            foreach (static::DB_TABLES as $table) {
                static::$_DB->query('DROP TABLE IF EXISTS `' . $table . '`');
            }
        }
    }

    /**
     * Getting filename of child class
     *
     * @return bool|string
     */
    protected static function _getPath(): bool|string
    {
        $cl = new ReflectionClass(static::class);
        return dirname($cl->getFileName());
        unset($cl);
    }
}