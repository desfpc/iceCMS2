<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Console app Class
 */

namespace iceCMS2\Cli;

use iceCMS2\Caching\CachingFactory;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class App
{
    /** @var Settings app settings */
    private Settings $_settings;

    /** @var array console arguments */
    private array $_argv;

    /**
     * Class constructor
     *
     * @param array $settings
     * @param array $argv
     * @throws Exception
     */
    public function __construct(array $settings, array $argv)
    {
        $this->_settings = new Settings($settings);
        if ($this->_settings->errors->flag === 1) {
            throw new Exception($this->_settings->errors->text);
        }
        if (!empty($argv)) {
            unset($argv[0]);
        }
        if (empty($argv)) {
            throw new Exception('No command found. Type "php cli.php help" for command help.');
        }
        $this->_argv = $argv;
        $this->_requestParsing();
    }

    /**
     * Parsing Request
     *
     * @return void
     * @throws Exception
     */
    private function _requestParsing(): void
    {
        switch ($this->_argv[1]) {
            case 'help':
                echo "\n" . 'IceCMS2 Help';
                echo "\n" . 'Type command after php cli.php:';
                echo "\n\n" . 'help - IceCMS2 Help';
                echo "\n";
                echo "\n" . 'migration-create {name} - Create blank new DB migration with name {name}. Name must be in CamelCase.';
                echo "\n" . 'migration-exec - Execute DB migrations.';
                echo "\n" . 'migration-rollback - Rollback last DB migration.';
                echo "\n";
                echo "\n" . 'cache-clear-all - Clear all caches.';
                echo "\n";
                echo "\n" . 'make-symlinks - Make symlinks from vendor to project folders';
                echo "\n\n";
                break;
            case 'migration-create':
                echo "\n" . 'IceCMS2 Migration Creating';
                if (empty($this->_argv[2])) {
                    $this->_argv[2] = null;
                }
                $migrations = new Migrations($this->_settings);
                if (!$migrations->create($this->_argv[2])) {
                    echo "\n\e[31m" . 'Error when trying create migration: ' . $migrations->getError() . "\e[39m";
                } else {
                    echo "\n\e[32m" . 'Migration created!' . "\e[39m";
                }
                echo "\n\n";
                break;
            case 'migration-exec':
                echo "\n" . 'IceCMS2 Migration Executing';
                $migrations = new Migrations($this->_settings);
                if (!$migrations->exec()) {
                    echo "\n\e[31m" . 'Error when trying execute migrations: ' . $migrations->getError() . "\e[39m";
                } else {
                    echo "\n\e[32m" . 'Migrations executed!' . "\e[39m";
                }
                echo "\n\n";
                break;
            case 'migration-rollback':
                echo "\n" . 'IceCMS2 Migration Rollback';
                $migrations = new Migrations($this->_settings);
                if (!$migrations->rollback()) {
                    echo "\n\e[31m" . 'Error when trying rollback migration: ' . $migrations->getError() . "\e[39m";
                } else {
                    echo "\n\e[32m" . 'Migration rollbacked!' . "\e[39m";
                }
                echo "\n\n";
                break;
            case 'cache-clear-all':
                echo "\n" . 'IceCMS2 Clear all caches';
                $cacher = CachingFactory::instance($this->_settings);
                $keys = $cacher->findKeys($this->_settings->db->name . '*');
                if (!empty($keys)) {
                    foreach ($keys as $key) {
                        echo "\n" . $key . ' ';
                        if ($cacher->del($key)) {
                            echo "\e[32m" . '[DELETED]' . "\e[39m";
                        } else {
                            echo "\e[31m" . '[ERROR]' . "\e[39m";
                        }
                    }
                }
                break;
            case 'make-symlinks':
                echo "\n" . 'IceCMS2 Make symlinks';

                $symlinks = [
                    '/vendor/desfpc/vuebootstrap/src' => '/web/js/vuebootstrap',
                ];

                foreach ($symlinks as $key => $value) {
                    echo "\n";

                    if (file_exists($this->_settings->path . $value)) {
                        unlink($this->_settings->path . $value);
                    }

                    if (symlink(
                        $this->_settings->path . $key,
                        $this->_settings->path . $value
                    )) {
                        echo "\e[32m" . $value . ' - [OK]' . "\e[39m";
                    } else {
                        echo "\e[31m" . $value . ' - [ERROR]' . "\e[39m";
                    }
                }

                echo "\n";

                break;
            default:
                echo "\n\e[31m" . 'Wrong command "' . $this->_argv[1] . '". Type "php cli.php help" for command help.' . "\e[39m";
                break;
        }
    }
}