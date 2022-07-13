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
    protected array $_values = [];

    /** @var ?array<string, mixed> Entity values that have been changed but not saved */
    protected ?array $_dirtyValues = null;

    /** @var bool If Entity gotten from DB flag */
    public bool $isLoaded = false;
    
    /** @var bool If Entity have dirty values */
    public bool $isDirty = false;

    /** @var DBInterface DB Resource */
    protected DBInterface $_DB;

    /** @var CachingInterface Cacher */
    protected CachingInterface $_cacher;

    /** @var Settings App settings */
    protected Settings $_settings;

    /** @var string Entity DB table name */
    protected string $_dbtable = '';

    /** @var int|null Entity ID */
    protected ?int $_id = null;

    /** @var array|null Entity DB columns */
    protected ?array $_cols = null;

    /**
     * Entity constructor class
     *
     * @param Settings $settings App settings
     * @param int|null $id Entity ID
     * @throws Exception
     */
    public function __construct(Settings $settings, ?int $id = null)
    {
        $this->_id = $id;
        $this->_settings = $settings;
        $this->_DB = DBFactory::get($this->_settings);
        $this->_DB->connect();
        $this->_cacher = CachingFactory::instance($this->_settings);

        $this->_getTableCols();

        if (!empty($id)) {
            $this->load();
        }
    }

    /**
     * Getting Entity value/values
     *
     * @param string|null $key
     * @return mixed|mixed[]|null
     * @throws Exception
     */
    public function get(?string $key = null)
    {
        if (is_null($key)) {
            return $this->_values;
        }
        if (!isset($this->_values[$key])) {
            throw new Exception('Field "' . $key . '" missing in table "' . $this->_dbtable . '"');
        }
        return $this->_values[$key];
    }

    /**
     * Getting Entity dirty values
     *
     * @return mixed[]|null
     */
    public function getDirty()
    {
        return $this->_dirtyValues;
    }

    /**
     * Setting new Entity value/values
     *
     * @param string|array $keyOrValues
     * @param string|int|float|bool|null $value
     * @param bool $checkKey
     * @return void
     * @throws Exception
     */
    public function set(string|array $keyOrValues, string|int|float|bool|null $value = null, bool $checkKey = true): void
    {
        if (is_string($keyOrValues)) {
            $this->_setByKeyAndValue($keyOrValues, $value, $checkKey);
        } else {
            if (!empty($keyOrValues)) {
                foreach ($keyOrValues as $key => $value) {
                    if ($checkKey && !isset($this->_cols[$key])) {
                        continue;
                    }
                    $this->_setByKeyAndValue($key, $value, $checkKey);
                }
            }
        }
    }

    /**
     * Setting new Entity value
     *
     * @param string $key
     * @param string|int|float|bool|null $value
     * @param bool $checkKey
     * @throws Exception
     */
    protected function _setByKeyAndValue(string $key, string|int|float|bool|null $value = null, bool $checkKey = true): void
    {
        if ($checkKey && !isset($this->_cols[$key])) {
            throw new Exception('Field "' . $key . '" missing in table "' . $this->_dbtable . '"');
        }
        if (!isset($this->_values[$key]) || $this->_values[$key] !== $value) {
            if (is_null($this->_values)) {
                $this->_values = [];
            }
            $this->isDirty = true;
            $this->_dirtyValues[$key] = !isset($this->_values[$key]) ? null : $this->_values[$key];
            $this->_values[$key] = $value;
        }
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
                    $this->_cols = [];
                    foreach ($res as $row) {
                        $this->_cols[$row['Field']] = $row;
                    }
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
        if ($this->isDirty && !empty($this->_values)) {
            list($prepariedSQL, $prepariedValues) = $this->_getEntitySaveData();
            print_r([$prepariedSQL, $prepariedValues]);
            if ($res = $this->_DB->queryBinded($prepariedSQL, $prepariedValues)) {
                if (is_null($this->_id)) {
                    if (is_int($res)) {
                        $this->_id = $res;
                    }
                }
                print_r($this->_DB->query('SELECT * FROM files'));

                if (!$this->load()) {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Get data for Entity save SQL query
     *
     * @return array<string, array>
     */
    private function _getEntitySaveData(): array
    {
        $binded = [];
        $keys = '';
        $bindedKeys = '';
        $updateStr = '';
        $i = 0;
        foreach ($this->_dirtyValues as $key => $val) {
            if (isset($this->_cols[$key])) {
                ++$i;
                if ($i > 1) {
                    $keys .= ', ';
                    $bindedKeys .= ', ';
                    $updateStr .= ', ';
                }
                $keys .= '`' . $key . '`';
                $bindedKeys .= '?';
                $updateStr .= '`' . $key . '`' . ' = ?';
                $binded[':' . $key] = $this->_values[$key];
            }
        }
        if (is_null($this->_id)) {
            $sql = 'INSERT INTO `' . $this->_dbtable . '` (' . $keys . ') VALUES (' . $bindedKeys . ')';
        } else {
            $sql = 'UPDATE `' . $this->_dbtable . '` SET ' . $updateStr . ' WHERE `id` = ' . $this->_id;
        }

        return [$sql, $binded];
    }

    /**
     * Delete Entity
     *
     * @param int|null $id
     * @return bool
     */
    public function del(?int $id = null): bool
    {
        $this->_needLoaded();

        $this->id = $id;

        if ($this->_beforeDel() && $res = $this->DB->query($this->_delEntityValuesSQL())) {
            $this->_uncacheRecord();
            $this->_id = null;
            $this->_values = null;
            $this->isLoaded = false;
            return true;
        }
        return false;
    }

    /**
     * Function before delete
     *
     * @return bool
     */
    protected function _beforeDel()
    {
        return true;
    }

    /**
     * Load Entity from DB by ID
     *
     * @param int|null $id
     * @return bool
     */
    public function load(?int $id = null): bool
    {
        $this->isDirty = false;
        $this->_dirtyValues = null;
        $this->isLoaded = false;
        $this->_values = [];

        if (!is_null($id)) {
            $this->_id = $id;
        }
        if (is_null($this->_id)) {
            return false;
        }

        $key = $this->_getCacheKey();

        if ($this->_cacher->has($key) && $values = $this->_cacher->get($key, true)) {
            $this->_values = $values;
            $this->isLoaded = true;
            return true;
        } elseif ($res = $this->_DB->query($this->_getEntityValuesSQL()) && count($res) > 0) {
            $this->_values = $res[0];
            $this->_afterLoad();
            $this->_cacheRecord();

            $this->isLoaded = true;
            return true;
        }
        return false;
    }

    /**
     * Load last Entity from DB by Parameter and Value
     *
     * @param string $param
     * @param string|int|float|bool|null $value
     * @return bool
     * @throws Exception
     */
    public function loadByParam(string $param, string|int|float|bool|null $value = null): bool
    {
        if (!isset($this->_values[$param])) {
            throw new Exception('Field "' . $param . '" missing in table "' . $this->_dbtable . '"');
        }

        $sql = 'SELECT max(`id`) `id` FROM `' . $this->_dbtable . '` WHERE `' . $param . '` = ?';
        $res = $this->_DB->queryBinded($sql, [':'.$param => $value]);
        if ($res === false) {
            return false;
        }
        $id = $res[0]['id'];

        return $this->load($id);
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
     * Some logics after Entity load() method. Fore extend in child classes.
     *
     * @return void
     */
    protected function _afterLoad(): void
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
        $this->_cacher->set($this->_getCacheKey(), json_encode($this->_values), $expired);
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

    /**
     * Function, calling before publick methods
     *
     * @return void
     */
    protected function _needLoaded(): void
    {
        if (!$this->isLoaded) {
            throw new Exception('You must load entity before calling this method');
        }
    }
}