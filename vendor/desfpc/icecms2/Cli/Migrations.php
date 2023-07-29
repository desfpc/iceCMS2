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

    /** @var string */
    private string $_migrationsFolder;

    /**
     * Constructor class
     */
    public function __construct(Settings $settings)
    {
        $this->_settings = $settings;
        $this->_migrationsFolder = $this->_settings->path . 'migrations' . DIRECTORY_SEPARATOR
            . $this->_settings->db->type . DIRECTORY_SEPARATOR;
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
        $fullName = $this->_migrationsFolder . date('YmdHis') . '_' . Strings::camelToSnake($name) . '.php';
        echo "\n" . 'Creating migration file ' . $fullName;

        $tempFile = $this->_migrationsFolder . 'template.txt';

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
        if (!$this->_isDB) {
            return false;
        }

        $migrationFolderFiles = scandir(substr($this->_migrationsFolder,0,-1));
        foreach ($migrationFolderFiles as $file) {
            if (!in_array($file, ['.', '..', 'template.txt'])) {
                $migration = $this->_getMigrationData($file);
                if (!$this->_isMigrationExecuted($migration['version'])) {
                    echo "\n" . 'Executing migration ' . $file;
                    include_once($this->_migrationsFolder . $file);
                    $mName = $this->_getFullClassName($migration['name']);
                    /** @var AbstractMigration $mObj */
                    $mObj = new $mName($this->_db, $migration['version'], $migration['name']);
                    if ($mObj->exec()) {
                        echo ' ... ' . "\e[32m" . 'DONE' . "\e[39m";
                    } else {
                        echo ' ... ' . "\e[31m" . 'ERROR' . "\e[39m";
                        $this->_errorText = $mObj->getErrorText();
                        return false;
                    }
                };
            }
        }
        return true;
    }

    /**
     * Getting migration class name vs namespace
     *
     * @param $name
     * @return string
     */
    private function _getFullClassName($name): string
    {
        return 'app\\migrations\\' . $this->_settings->db->type . '\\' . $name;
    }

    /**
     * Check is migration executed
     *
     * @param string $version
     * @return bool
     */
    private function _isMigrationExecuted(string $version): bool
    {
        return (bool)($this->_db->query('SELECT * FROM `migrations` WHERE `version` = '
            . $this->_db->realEscapeString($version)));
    }

    /**
     * Rollback last migration
     *
     * @return bool
     */
    public function rollback(): bool
    {
        if (!$this->_isDB) {
            return false;
        }

        if ($res = $this->_db->query('SELECT `version`, `name` FROM `migrations` ORDER BY `version` DESC LIMIT 0, 1')) {
            $lastTransaction = $res[0];
            $file = $lastTransaction['version'] . '_' . Strings::camelToSnake($lastTransaction['name']) . '.php';
            $migration = $this->_getMigrationData($file);
            echo "\n" . 'Rollbacking migration ' . $file;
            include_once($this->_migrationsFolder . $file);
            $mName = $this->_getFullClassName($migration['name']);
            /** @var AbstractMigration $mObj */
            $mObj = new $mName($this->_db, $migration['version'], $migration['name']);
            if ($mObj->rollback()) {
                echo ' ... ' . "\e[32m" . 'DONE' . "\e[39m";
            } else {
                echo ' ... ' . "\e[31m" . 'ERROR' . "\e[39m";
                $this->_errorText = $mObj->getErrorText();
                return false;
            }
        }

        return true;
    }

    /**
     * Get migration array
     *
     * @param string $migrationName
     * @return array
     */
    private function _getMigrationData(string $migrationName): array
    {
        $content = file_get_contents($this->_migrationsFolder . $migrationName);
        preg_match('/class (.*) extends AbstractMigration/', $content, $name);

        return [
            'file' => $migrationName,
            'version' => (explode('_', $migrationName))[0],
            'name' => $name[1],
        ];
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