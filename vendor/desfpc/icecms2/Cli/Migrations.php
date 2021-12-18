<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Migration processing Class
 */

namespace iceCMS2\Cli;

use iceCMS2\DB\DBInterface;
use iceCMS2\DB\DBFactory;
use iceCMS2\Settings\Settings;
use iceCMS2\Helpers\Strings;
use PhpParser\Error;

class Migrations
{
    /** @var Settings App settings */
    private Settings $_settings;

    /**
     * @var string Error text
     */
    private string $_errorText = '';

    /** @var DBInterface|null DB resourse */
    private ?DBInterface $_db;

    /** @var bool DB resourse flag */
    private bool $_isDB = false;

    /**
     * Constructor class
     */
    public function __construct(Settings $settings)
    {
        $this->_settings = $settings;
        $this->_db = DBFactory::get($this->_settings);
        if (is_null($this->_db)) {
            $this->_errorText = 'Wrong DB settings';
        } elseif (!$this->_db->connect()) {
            $this->_errorText = $this->_db->getErrorText();
        } else {
            $this->_isDB = true;
        }
    }

    /**
     * Create new blank migration
     *
     * @param string|null $name New migration name
     * @return bool
     */
    public function create(?string $name = null): bool
    {
        if (!$this->_isDB) {
            return false;
        }
        if (empty($name)) {
            $name = 'CustomMigration';
        } else {
            $name = str_replace(' ', '', $name);
        }
        $fullName = $this->_settings->path . 'migrations' . DIRECTORY_SEPARATOR . $this->_settings->db->type
            . DIRECTORY_SEPARATOR . date('YmdHis') . '_' . Strings::camelToSnake($name) . '.php';
        echo "\n" . 'Creating migration file ' . $fullName;

        $tempFile = $this->_settings->path . 'migrations' . DIRECTORY_SEPARATOR . $this->_settings->db->type
            . DIRECTORY_SEPARATOR . 'template.txt';

        if (!$template = file_get_contents($tempFile)) {
            $this->_errorText = "\n\e[31m" . 'Error when trying read migration template file: ' . $fullName . "\e[39m";
            return false;
        }

        $template = str_replace('{DB Migration Template - DO NOT DELETE}', $name . ' DB Migration', $template);
        $template = str_replace('{MigrationClass}', $name, $template);

        if (!file_put_contents($fullName, $template)) {
            $this->_errorText = "\n\e[31m" . 'Error when trying save migration file: ' . $fullName . "\e[39m";
            return false;
        }

        return true;
    }

    /**
     * Execute migrations
     *
     * @return bool
     */
    public function exec(): bool
    {
        // TODO do it
        return false;
    }

    /**
     * Rollback last migration
     *
     * @return bool
     */
    public function rollback(): bool
    {
        // TODO do it
        return false;
    }

    /**
     * Get error text
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->_errorText;
    }
}