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

use iceCMS2\Settings\Settings;

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
     */
    public function __construct(array $settings, array $argv)
    {
        $this->_settings = new Settings($settings);
        if ($this->_settings->errors->flag === 1) {
            die ($this->_settings->errors->text);
        }
        if (!empty($argv)) {
            unset($argv[0]);
        }
        if (empty($argv)) {
            die('No command found. Type "php cli.php help" for command help.');
        }
        $this->_argv = $argv;
        $this->_requestParsing();
    }

    /**
     * Parsing Request
     *
     * @return void
     */
    private function _requestParsing(): void
    {
        switch ($this->_argv[1]) {
            case 'help':
                echo "\n" . 'IceCMS2 Help';
                echo "\n" . 'Type command after php cli.php:';
                echo "\n\n" . 'help - IceCMS2 Help';
                echo "\n";
                echo "\n" . 'migration-create {name} - Create blank new DB migration with name {name}';
                echo "\n" . 'migration-exec - Execute DB migrations';
                echo "\n" . 'migration-rollback - Rollback last DB migration';
                echo "\n\n";
                break;
            case 'migration-create':
                echo "\n" . 'IceCMS2 Migration Creating';
                if (empty($argv[2])) {
                    $argv[2] = null;
                }
                $migrations = new Migrations($this->_settings);
                if (!$migrations->create($argv[2])) {
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
            default:
                echo "\n\e[31m" . 'Wrong command "' . $this->_argv[1] . '". Type "php cli.php help" for command help.' . "\e[39m";
                break;
        }
    }
}