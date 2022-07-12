<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * TestCase class - setup site settings and test DB (if static $_dbTables not empty in test class)
 */

namespace iceCMS2\Tests;

use desfpc\Visualijoper\Visualijoper;
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
    protected static array $_dbTables = [];

    /**
     * @var Settings|null App settings
     */
    protected static ?Settings $_settings;

    /**
     * @var Settings|null App settings
     */
    protected static ?Settings $_testSettings;

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
        echo PHP_EOL . 'setUpBeforeClass for class ' . static::class . PHP_EOL;
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

        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $dir = DIRECTORY_SEPARATOR . $dir;
        }

        if (empty(self::$_settings)) {
            $settings = [];
            require $dir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'settingsSelector.php';
            if (!empty($settings)) {
                self::$_settings = new Settings($settings);
                self::$_testSettings = clone self::$_settings;
                self::$_testSettings->db = self::$_testSettings->db_test;
            }
        }

        if (!empty(self::$_settings)) {
            static::$_realDB = (new DBFactory(self::$_settings))->DB;
            static::$_DB = (new DBFactory(self::$_testSettings))->DB;

            //Copy Tables structure from real DB to test DB
            if (!empty(static::$_dbTables)) {
                static::$_realDB->connect();
                static::$_DB->connect();
                static::$_DB->query('SET foreign_key_checks = 0;');
                foreach (static::$_dbTables as $table) {
                    echo PHP_EOL . 'Creating table ' . $table . '...';
                    if ($createTableSQL = static::$_realDB->query('SHOW CREATE TABLE `' . $table . '`;')) {
                        $createTableSQL = $createTableSQL[0]['Create Table'];
                        if (static::$_DB->query($createTableSQL)) {
                            echo "\nTest table " . $table . ' created';
                        } else {
                            print_r(static::$_DB);
                        }
                    } else {
                        print_r(static::$_realDB);
                    }
                    //Insert test Data - find static::$_dbTables json file for insert
                    $testDataFilePath = static::_getPath() . DIRECTORY_SEPARATOR . $table . '.json';
                    if (file_exists($testDataFilePath) && $data = file_get_contents($testDataFilePath)) {
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
                static::$_DB->query('SET foreign_key_checks = 1;');
                static::$_realDB->disconnect();
            }
        } else {
            echo PHP_EOL . 'Empty Settings!';
        }
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        echo PHP_EOL . 'tearDownAfterClass for class ' . static::class . PHP_EOL;
        if (!empty(static::$_dbTables)) {
            foreach (static::$_dbTables as $table) {
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