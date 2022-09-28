<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * AbstractLogEntity class
 */

namespace iceCMS2\Models;

use iceCMS2\Locale\LocaleText;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

abstract class AbstractLogEntity extends AbstractEntity
{
    /** @var string|null Main log tible (for copying structure) */
    protected ?string $_mainTable = null;

    /**
     * @inheritDoc
     */
    public function __construct(Settings $settings, int|array|null $id = null)
    {
        $this->_settings = $settings;
        $this->_mainTable = $this->_dbtable;
        $this->_dbtable = $this->_getTableName();
        parent::__construct($settings, $id);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _ifGetTableColsError(): void
    {
        //Checking for table exist and creating if not
        if (!$this->_ifLogTableExists() && !$this->_createLogTable()) {
            throw new Exception(LocaleText::get(
                $this->_settings,
                'log/errors/Error creating log table: {tableName}',
                ['tableName' => $this->_dbtable]
            ));
        }
    }

    /**
     * Get table name according to log period and now time
     *
     * @return string
     */
    private function _getTableName(): string
    {
        return match ($this->_settings->logs->period) {
            'month' => $this->_mainTable . '_' . date('Ym'),
            'year' => $this->_mainTable . '_' . date('Y'),
            default => $this->_mainTable . '_log',
        };
    }

    /**
     * Checking if real log table is exists
     *
     * @return bool
     */
    private function _ifLogTableExists(): bool
    {
        $res = $this->_db->query('SHOW TABLES LIKE "' . $this->_dbtable . '"');
        return !empty($res);
    }

    /**
     * Creating log table for now time
     *
     * @return bool
     */
    private function _createLogTable(): bool
    {
        if ($createTableSQL = $this->_db->query('SHOW CREATE TABLE `' . $this->_mainTable . '`;')) {
            $createTableSQL = $createTableSQL[0]['Create Table'];
            $createTableSQL = str_replace(
                'CREATE TABLE `' . $this->_mainTable . '`',
                'CREATE TABLE `' . $this->_dbtable . '`',
                $createTableSQL
            );
            return $this->_db->query($createTableSQL);
        }

        return false;
    }
}