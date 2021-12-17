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

    /** @var ?string */
    protected ?string $_errorText = null;

    /** @var bool */
    protected bool $_isConnectionError = true;

    /**
     * @var DBInterface|null DR Resourse
     */
    protected ?DBInterface $_db = null;

    /**
     * Class constructor
     *
     * @param DBInterface $DB
     */
    public function __construct(DBInterface $DB)
    {
        $this->_db = $DB;
        if (!$this->_db->getConnected()) {
            $this->_db->connect();
        }
        if (!$this->_db->getConnected()) {
            $this->_isConnectionError = true;
            $this->_errorText = 'DB Connection error: ' . $this->_db->getErrorText();
        }
    }

    /**
     * Execute Migration
     *
     * @return bool
     */
    public function exec(): bool
    {
        $this->sql = $this->up();
        return $this->_request();
    }

    /**
     * Rollback Migration
     *
     * @return bool
     */
    public function rollback(): bool
    {
        $this->sql = $this->down();
        return $this->_request();
    }

    /**
     * Execute migration query
     *
     * @return bool
     */
    public function up(): string
    {
    }

    /**
     * Roolback migration query
     *
     * @return bool
     */
    public function down(): string
    {
    }

    /**
     * Execute SQL request
     *
     * @return bool
     */
    protected function _request(): bool
    {
        if (is_null($this->sql)) {
            return true;
        }
        if ($this->_isConnectionError) {
            return false;
        }
        return $this->_db->multiQuery($this->sql);
    }
}