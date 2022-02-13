<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Abstract entity class
 */

namespace iceCMS2\Models;

use iceCMS2\Caching\CachingFactory;
use iceCMS2\DB\DBFactory;
use iceCMS2\DB\DBInterface;
use iceCMS2\Caching\CachingInterface;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

abstract class AbstractEntity
{
    /** @var string[] Entity errors */
    public array $errors = [];
    /** @var ?array<string, mixed> Entity values */
    public ?array $values = null;
    /** @var bool If Entity gotten from DB flag */
    public bool $isGotten = false;
    /** @var DBInterface DB Resource */
    private DBInterface $_DB;
    /** @var CachingInterface Cacher */
    private CachingInterface $_cacher;
    /** @var Settings App settings */
    private Settings $_settings;
    /** @var string Entity DB table name */
    private string $_dbtable;
    /** @var int|null Entity ID */
    private ?int $_id = null;
    /** @var array|null Entity DB columns */
    private ?array $_cols = null;

    /**
     * Entity constructor class
     *
     * @param Settings $settings App settings
     */
    public function __construct(Settings $settings, string $dtable, ?int $id = null)
    {
        $this->_id = $id;
        $this->_dbtable = $dtable;
        $this->_settings = $settings;
        $this->_DB = DBFactory::get($this->_settings);
        $this->_cacher = CachingFactory::instance($this->_settings);

        $this->_getTableCols();
    }

    /**
     * Getting Entity DB table columns
     *
     * @return void
     */
    private function _getTableCols(): void
    {
        $key = $this->_getTableColsKey();
        $cols = [];

        if ($this->_cacher->has($key) && $cols = $this->_cacher->get($key, true)) {
            $this->_cols = $cols;
        } else {
            $query = 'SHOW COLUMNS FROM ' . $this->_dbtable;

            if ($res = $this->_DB->query($query)) {
                if (count($res) > 0) {
                    $this->_cols = $res;
                    $this->_cacher->set($key, json_encode($this->_cols));
                }
            }
        }
    }

    /**
     * Get cacher key for Entity BD table
     *
     * @return string
     */
    private function _getTableColsKey(): string
    {
        return $this->_settings->db->name . '_tableCols_' . $this->_dbtable;
    }

    /**
     * Save Entity
     *
     * @return bool
     */
    public function save(): bool
    {
        if (!empty($this->values)) {
            if ($res = $this->_DB->queryBinded($this->_getEntitySaveSQL(), $this->_getEntitySaveValues())) {
                if (is_null($this->_id)) {
                    if (is_int($res)) {
                        $this->_id = $res;
                    }
                }
                return true;
            }
        }
        return false;
    }

    //TODO _getEntitySaveSQL
    //TODO _getEntitySaveValues

    /**
     * Delete Entity
     *
     * @param int|null $id
     * @return bool
     */
    public function del(?int $id = null): bool
    {
        $this->id = $id;

        if ($res = $this->DB->query($this->_delEntityValuesSQL())) {
            $this->_uncacheRecord();
            $this->_id = null;
            $this->values = false;
            $this->isGotten = false;
            return true;
        }
        return false;
    }

    /**
     * Getting Entity from DB by ID
     *
     * @param int|null $id
     * @return bool
     */
    public function get(?int $id = null): bool
    {
        $this->isGotten = false;
        $this->values = null;

        if (!is_null($id)) {
            $this->_id = id;
        }
        if (is_null($this->_id)) {
            return false;
        }

        $key = $this->_getCacheKey();

        if ($this->_cacher->has($key) && $values = $this->_cacher->get($key, true)) {
            $this->values = $values;
            $this->isGotten = true;
            return true;
        } elseif ($res = $this->_DB->query($this->_getEntityValuesSQL()) && count($res) > 0) {
            $this->values = $res[0];
            $this->_afterGet();
            $this->_cacheRecord();

            $this->isGotten = true;
            return true;
        }
        return false;
    }

    /**
     * Get cacher key for Entity
     *
     * @return string
     */
    private function _getCacheKey(): string
    {
        if (is_null($this->_id)) {
            $id = '';
        } else {
            $id = $this->_id;
        }
        return $this->_settings->db->name . '_record_' . $this->_dbtable . '_' . $id;
    }

    /**
     * Getting SQL string for deleting Entity from DB
     *
     * @return string
     * @throws Exception
     */
    protected function _delEntityValuesSQL(): string
    {
        if (is_null($this->_id)) {
            throw new Exception('Entity has no ID');
        }
        return 'DELETE FROM ' . $this->_dbtable . ' WHERE id = ' . $this->_id;
    }

    /**
     * Getting SQL string for filling Entity values query
     *
     * @return string
     * @throws Exception
     */
    protected function _getEntityValuesSQL(): string
    {
        if (is_null($this->_id)) {
            throw new Exception('Entity has no ID');
        }
        return 'SELECT * FROM ' . $this->_dbtable . ' WHERE id = ' . $this->_id;
    }

    /**
     * Some logics after Entity get() method. Fore extend in child classes.
     *
     * @return void
     */
    protected function _afterGet(): void
    {
    }

    /**
     * Cache Entity values
     *
     * @param int|float $expired
     * @throws \Exception
     */
    private function _cacheRecord(int $expired = 30 * 24 * 60 * 60)
    {
        $this->_cacher->set($this->_getCacheKey(), json_encode($this->values), $expired);
    }

    /**
     * Clear (refresh) Entity Cache
     *
     * @throws \Exception
     */
    public function clearCache()
    {
        $this->_uncacheRecord();
        $this->_refreshTableCols();
    }

    /**
     * Uncache Entity values
     *
     * @throws \Exception
     */
    private function _uncacheRecord()
    {
        $this->_cacher->del($this->_getCacheKey());
    }

    /**
     * Refresh Entity table columns data
     *
     * @throws \Exception
     */
    private function _refreshTableCols(): void
    {
        $key = $this->_getTableColsKey();
        $this->_cacher->del($key);
        $this->_getTableCols();
    }
}