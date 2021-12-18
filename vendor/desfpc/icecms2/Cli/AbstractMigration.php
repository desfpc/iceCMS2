<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Abstract Migration Class
 */

namespace iceCMS2\Cli;

use iceCMS2\DB\DBInterface;

abstract class AbstractMigration
{
    /** @var ?string migration SQL for executing */
    public ?string $sql = null;

    /** @var string */
    protected ?string $_errorText = '';

    /** @var bool */
    protected bool $_isConnectionError = false;

    /**
     * @var DBInterface|null DR Resourse
     */
    protected ?DBInterface $_db = null;

    /**
     * @var string Migration version (datetime string)
     */
    protected string $_version;

    /**
     * @var string Migration name
     */
    protected string $_name;

    /**
     * Class constructor
     *
     * @param DBInterface $DB
     */
    public function __construct(DBInterface $DB, string $version, string $name)
    {
        //properties initialization
        $this->_version = $version;
        $this->_name = $name;
        $this->_db = $DB;

        //DB connection
        if (!$this->_db->getConnected()) {
            $this->_db->connect();
        }
        if (!$this->_db->getConnected()) {
            $this->_isConnectionError = true;
            $this->_errorText = 'DB Connection error: ' . $this->_db->getErrorText();
        } else {
            //Check and create migrations table if needed
            if (!$res = $this->_db->query('SELECT count(`version`) `mcnt` FROM `migrations`')) {
                if (!$this->_db->createMigrationTable()) {
                    $this->_isConnectionError = true;
                    $this->_errorText = 'Creating migration table error: ' . $this->_db->getWarningText();
                }
            };
        }
    }

    /**
     * Execute Migration
     *
     * @return bool
     */
    public function exec(): bool
    {
        $this->sql = $this->_makeFullSQL($this->up());
        return $this->_request();
    }

    /**
     * Execute migration query
     *
     * @return string
     */
    public function up(): string
    {
    }

    /**
     * Execute SQL request
     *
     * @return bool
     */
    protected function _request(): bool
    {
        if (empty($this->sql)) {
            return true;
        }
        if ($this->_isConnectionError) {
            return false;
        }
        if (!$this->_db->transaction($this->sql)) {
            $this->_errorText = $this->_db->getWarningText();
            return false;
        }
        return true;
    }

    /**
     * Rollback Migration
     *
     * @return bool
     */
    public function rollback(): bool
    {
        $this->sql = $this->_makeFullSQL($this->down());
        return $this->_request();
    }

    /**
     * Roolback migration query
     *
     * @return string
     */
    public function down(): string
    {
    }

    /**
     * Get Error Text
     *
     * @return string
     */
    public function getErrorText(): string
    {
        return $this->_errorText;
    }

    protected function _makeFullSQL(string $sql)
    {
        return "INSERT INTO `migrations`(`version`, `name`) \n"
            . "VALUES(" . $this->_version . ", '" . $this->_name . "');"
            . "\n" . $sql . "\n"
            . 'UPDATE `migrations` SET `end_time` = NOW() WHERE `version` = ' . $this->_version . ';';
    }
}