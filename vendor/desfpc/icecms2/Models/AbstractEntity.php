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
use iceCMS2\Types\UnixTime;

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
    protected DBInterface $_db;

    /** @var CachingInterface Cacher */
    protected CachingInterface $_cacher;

    /** @var Settings App settings */
    protected Settings $_settings;

    /** @var string Entity DB table name */
    protected string $_dbtable = '';

    /** @var int|null Entity ID */
    protected ?int $_id = null;

    /** @var ?array table columns=>values for ID (that create primary key or uniq ID for table without ID column) */
    protected ?array $_idKeys = null;

    /** @var array|null columns for ID */
    protected ?array $_idColumns = null;

    /** @var array|null Entity DB columns */
    protected ?array $_cols = null;

    /**
     * Entity constructor class
     *
     * @param Settings $settings App settings
     * @param int|array|null $id Entity ID
     * @throws Exception
     */
    public function __construct(Settings $settings, int|array|null $id = null)
    {
        if (is_int($id)) {
            $this->_id = $id;
        } elseif (is_array($id)) {
            $this->_idKeys = $id;
        }
        $this->_settings = $settings;
        $this->_db = DBFactory::get($this->_settings);
        $this->_db->connect();
        $this->_cacher = CachingFactory::instance($this->_settings);

        $this->_getTableCols();

        if (!empty($id)) {
            $this->load();
        }
    }

    /**
     * Getting entity's class key (class name by default)
     * For example to link image sizes with the type of the current object
     *
     * @return string
     */
    public function getKeyString(): string
    {
        return static::class;
    }

    /**
     * Getting Entity value/values
     *
     * @param string|null $key
     * @return mixed|array|null
     * @throws Exception
     */
    public function get(?string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->_values;
        }
        if (!key_exists($key, $this->_values)) {
            throw new Exception('Field "' . $key . '" missing in table "' . $this->_dbtable . '"');
        }
        return $this->_values[$key];
    }

    /**
     * Getting Entity dirty values
     *
     * @return array|null
     */
    public function getDirty(): ?array
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
    public function set(string|array $keyOrValues, string|int|float|bool|UnixTime|null $value = null, bool $checkKey = true): void
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
    protected function _setByKeyAndValue(string $key, string|int|float|bool|UnixTime|null $value = null, bool $checkKey = true): void
    {
        if ($checkKey && !isset($this->_cols[$key])) {
            throw new Exception('Field "' . $key . '" missing in table "' . $this->_dbtable . '"');
        }
        if (!isset($this->_values[$key]) || $this->_values[$key] !== $value) {
            $this->isDirty = true;
            $this->_dirtyValues[$key] = !isset($this->_values[$key]) ? null : $this->_values[$key];
            $this->_values[$key] = $value;
        }
    }

    /**
     * Getting Entity DB table columns
     *
     * @return void
     * @throws Exception
     */
    protected function _getTableCols(): void
    {
        $key = $this->_getTableColsKey();

        if ($this->_cacher->has($key) && $cols = $this->_cacher->get($key, true)) {
            $this->_cols = $cols;
        } else {
            $query = 'SHOW COLUMNS FROM ' . $this->_dbtable;

            if ($res = $this->_db->query($query)) {
                if (count($res) > 0) {
                    $this->_cols = [];
                    foreach ($res as $row) {
                        $this->_cols[$row['Field']] = $row;
                    }
                    $this->_cacher->set($key, json_encode($this->_cols));
                }
            } else {
                $this->_ifGetTableColsError();
                $this->_getTableCols();
            }
        }
    }

    /**
     * Function that runs when get error while getting Entity DB table columns
     *
     * @return void
     */
    protected function _ifGetTableColsError(): void
    {

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
     * Setting _idKeys from _idColumns and _values
     *
     * @return void
     */
    private function _setIdKeys(): void
    {
        foreach ($this->_idColumns as $key) {
            $this->_idKeys[$key] = $this->_values[$key];
        }
    }

    /**
     * Save Entity
     *
     * @param bool $isUpdateOnDuplicateKey
     * @return bool
     * @throws Exception
     */
    public function save(bool $isUpdateOnDuplicateKey = false): bool
    {
        $this->_idKeys = null;
        if ($this->isDirty && !empty($this->_values)) {
            /**
             * @var string $preparedSQL
             * @var array $prepariedValues
             */
            [$preparedSQL, $preparedValues] = $this->_getEntitySaveData($isUpdateOnDuplicateKey);
            if ($res = $this->_db->queryBinded($preparedSQL, $preparedValues)) {
                if (is_null($this->_id) && empty($this->_idKeys)) {
                    if (is_int($res) && is_null($this->_idColumns)) {
                        $this->_id = $res;
                    } elseif ($res === true && !empty($this->_idColumns)) {
                        $this->_setIdKeys();
                    }
                }
                $this->_cacher->del($this->_getCacheKey());

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
     * @param bool $isUpdateOnDuplicateKey
     * @return array<string, array>
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function _getEntitySaveData(bool $isUpdateOnDuplicateKey = false): array
    {
        $binded = [];
        $bindedU = [];
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

                if ($this->_values[$key] instanceof UnixTime) {
                    $symbol = 'FROM_UNIXTIME(?)';
                } else {
                    $symbol = '?';
                }

                $bindedKeys .= $symbol;
                $updateStr .= '`' . $key . '`' . ' = ' . $symbol;
                $binded[':' . $key] = $this->_values[$key];
                $bindedU[':' . $key . '_u'] = $this->_values[$key];
            }
        }
        if (is_null($this->_id) && empty($this->_idKeys)) {
            $sql = 'INSERT INTO `' . $this->_dbtable . '` (' . $keys . ') VALUES (' . $bindedKeys . ')';
            if ($isUpdateOnDuplicateKey) {
                $sql .= ' ON DUPLICATE KEY UPDATE ' . $updateStr;
                $binded = array_merge($binded, $bindedU);
            }
        } else {
            $sql = 'UPDATE `' . $this->_dbtable . '` SET ' . $updateStr . ' WHERE';
            if (!is_null($this->_id)) {
                $sql .= ' `id` = ' . $this->_id;
            } else {
                $sql .= $this->_getIdKeysSQL();
            }
        }

        return [$sql, $binded];
    }

    /**
     * Delete Entity
     *
     * @param int|null $id
     * @return bool
     * @throws Exception
     */
    public function del(?int $id = null): bool
    {
        $this->_needLoaded();

        if (!is_null($id)) {
            $this->_id = $id;
        }

        if ($this->_beforeDel() && $this->_db->query($this->_delEntityValuesSQL())) {
            $this->_uncacheRecord();
            $this->_id = null;
            $this->_values = [];
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
    protected function _beforeDel(): bool
    {
        return true;
    }

    /**
     * Load Entity from DB by ID
     *
     * @param int|array|null $id
     * @return bool
     * @throws Exception
     */
    public function load(int|array|null $id = null): bool
    {
        $this->isDirty = false;
        $this->_dirtyValues = null;
        $this->isLoaded = false;
        $this->_values = [];

        if (is_int($id)) {
            $this->_id = $id;
        } elseif (is_array($id)) {
            $this->_idKeys = $id;
        }
        if (is_null($this->_id) && empty($this->_idKeys)) {
            return false;
        }

        $key = $this->_getCacheKey();
        if ($this->_cacher->has($key) && $values = $this->_cacher->get($key, true)) {
            $this->_values = $values;
            $this->isLoaded = true;
            return true;
        } elseif ($res = $this->_db->query($this->_getEntityValuesSQL())) {
            if (count($res) > 0) {
                $this->_values = $res[0];
                $this->_afterLoad();
                $this->_cacheRecord();

                $this->isLoaded = true;
                return true;
            }
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
        $res = $this->_db->queryBinded($sql, [':'.$param => $value]);
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
    protected function _getCacheKey(): string
    {
        $id = '';
        if (!is_null($this->_id)) {
            $id = '_' . $this->_id;
        } elseif(!empty($this->_idKeys)) {
            foreach ($this->_idKeys as $key => $value) {
                $id .= '_' . $key . '_' . $value;
            }
        }

        return $this->_settings->db->name . '_record_' . $this->_dbtable . $id;
    }

    /**
     * Getting SQL string for deleting Entity from DB
     *
     * @return string
     * @throws Exception
     */
    protected function _delEntityValuesSQL(): string
    {
        if (is_null($this->_id) && empty($this->_idKeys)) {
            throw new Exception('Entity has no ID');
        }
        if (!is_null($this->_id)) {
            return 'DELETE FROM `' . $this->_dbtable . '` WHERE `id` = ' . $this->_id;
        }
        return 'DELETE FROM `' . $this->_dbtable . '` WHERE' . $this->_getIdKeysSQL();
    }

    /**
     * Getting SQL string for filling Entity values query
     *
     * @return string
     * @throws Exception
     */
    protected function _getEntityValuesSQL(): string
    {
        if (is_null($this->_id) && empty($this->_idKeys)) {
            throw new Exception('Entity has no ID');
        }
        if (!is_null($this->_id)) {
            return 'SELECT * FROM `' . $this->_dbtable . '` WHERE `id` = ' . $this->_id;
        }
        return 'SELECT * FROM `' . $this->_dbtable . '` WHERE' . $this->_getIdKeysSQL();
    }

    /**
     * Get SQL for select record WS _idKeys
     *
     * @return string
     */
    private function _getIdKeysSQL(): string
    {
        $query = '';
        foreach ($this->_idKeys as $key => $value) {
            if (is_int($value) || is_float($value)) {
                $valueStr = $value;
            } elseif (is_null($value)) {
                $valueStr = 'NULL';
            } else {
                $valueStr = "'$value'";
            }

            if ($query !== '') {
                $query .= ' AND';
            }
            $query .= ' `' . $key . '` = ' . $valueStr;
        }

        return $query;
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
     * @param int $expired
     * @throws Exception
     */
    private function _cacheRecord(int $expired = 30 * 24 * 60 * 60): void
    {
        $this->_cacher->set($this->_getCacheKey(), json_encode($this->_values), $expired);
    }

    /**
     * Clear (refresh) Entity Cache
     *
     * @throws Exception
     */
    public function clearCache(): void
    {
        $this->_uncacheRecord();
        $this->_refreshTableCols();
    }

    /**
     * Uncache Entity values
     *
     * @throws Exception
     */
    private function _uncacheRecord(): void
    {
        $this->_cacher->del($this->_getCacheKey());
    }

    /**
     * Refresh Entity table columns data
     *
     * @throws Exception
     */
    private function _refreshTableCols(): void
    {
        $key = $this->_getTableColsKey();
        $this->_cacher->del($key);
        $this->_getTableCols();
    }

    /**
     * Function, calling before public methods
     *
     * @return void
     * @throws Exception
     */
    protected function _needLoaded(): void
    {
        if (!$this->isLoaded) {
            throw new Exception('You must load entity before calling this method');
        }
    }
}