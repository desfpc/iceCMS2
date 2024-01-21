<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Abstract entityes list class
 */

namespace iceCMS2\Models;

use iceCMS2\Caching\CachingInterface;
use iceCMS2\DB\DBFactory;
use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

abstract class AbstractEntityList
{
    /** @var DBInterface DB Resource */
    protected DBInterface $_db;

    /** @var CachingInterface Cacher */
    protected CachingInterface $_cacher;

    /** @var int Query caching time */
    protected int $_cacheTime = 0;

    /** @var Settings App settings */
    protected Settings $_settings;

    /** @var string Entity DB table name */
    protected string $_dbtable;

    /** @var array Query conditions data */
    protected array $_conditions;

    /** @var array Query order data */
    protected array $_order;

    /** @var ?array Fields for select */
    protected ?array $_selectedFields = null;

    /** @var int Query results page */
    protected int $_page = 1;

    /** @var ?int Query results count (page size) */
    protected ?int $_size = 10;

    /** @var ?int Query results cache in seconds */
    protected ?int $_cacheSeconds = 0;

    /** @var array|null columns for ID */
    protected ?array $_idColumns = null;

    /**
     * Entity list constructor
     *
     * @param Settings $settings
     * @param array $conditions
     * @param array $order
     * @param int $page
     * @param ?int $size
     * @param int $cacheSeconds
     * @throws Exception
     */
    public function __construct(
        Settings $settings,
        array $conditions = [],
        array $order = [],
        int $page = 1,
        ?int $size = 10,
        int $cacheSeconds = 0
    ) {
        $this->_settings = $settings;
        $this->_db = DBFactory::get($this->_settings);
        $this->_conditions = $conditions;
        $this->_order = $order;
        $this->_page = $page;
        $this->_size = $size;
        $this->_cacheSeconds = $cacheSeconds;
    }

    /**
     * Getting entity count according to conditions
     *
     * @return int|bool
     */
    public function getCnt(): int|bool
    {
        [$query, $bindedParams] = $this->_getFullQuery(true);

        if ($res = $this->_db->queryBinded($query, $bindedParams)) {
            return (int) $res[0]['cnt'];
        }
        return false;
    }

    /**
     * Getting array of entity's
     *
     * @return array|bool|int
     * @throws Exception
     */
    public function get(): array|bool|int
    {
        [$query, $bindedParams] = $this->_getFullQuery();

        if ($this->_cacheSeconds > 0) {

            $key = $this->_getQueryCacheKey($query, $bindedParams);

            if ($this->_cacher->has($key)) {
                return $this->_cacher->get($key);
            } else {
                $res = $this->_db->queryBinded($query, $bindedParams);
                $this->_cacher->set($key, $res, $this->_cacheSeconds);
                return $res;
            }
        }

        return $this->_db->queryBinded($query, $bindedParams);
    }

    /**
     * Get cache key for list query
     *
     * @param string $query
     * @param array $bindedParams
     * @return string
     */
    protected function _getQueryCacheKey(string $query, array $bindedParams): string
    {
        return $this->_settings->db->name . '_' . $this->_dbtable . '_' . md5($query) . '_' . md5(serialize($bindedParams));
    }

    /**
     * Getting full query and binding params
     *
     * @param bool $ifCnt
     * @return array
     */
    protected function _getFullQuery(bool $ifCnt = false): array
    {
        $query = $this->_getSelectQuery($ifCnt) . ' ' . $this->_getJoinQuery();
        $conditions = $this->_getConditionsQuery();
        $bindedParams = $conditions['bindedParams'];
        $query .= ' ' . $conditions['query'];

        if (!$ifCnt) {
            $query .= ' ' . $this->_getOrderQuery() . ' ' . $this->_getLimitQuery();
        }

        return [$query, $bindedParams];
    }

    /**
     * Getting select query part
     *
     * @param bool $ifCnt
     * @return string
     */
    protected function _getSelectQuery(bool $ifCnt = false): string
    {
        if ($ifCnt) {
            if (is_null($this->_idColumns)) {
                $query = 'SELECT COUNT(`dbtable`.`id`) `cnt` ';
            } else {
                $query = 'SELECT COUNT(*) `cnt` ';
            }
        } else {
            if (is_null($this->_selectedFields)) {
                $query = 'SELECT `dbtable`.* ';
            } else {
                $query = 'SELECT `dbtable`.`' . implode('`,`dbtable`.`', $this->_selectedFields) . '` ';
            }
        }
        $query .= $this->_getMoreSelectQuery();
        $query .= 'FROM ' . $this->_dbtable .' `dbtable`';
        $query .= ' ' . $this->_getMoreFromQuery();

        return $query;
    }

    /**
     * Getting more select query part
     *
     * @return string
     */
    protected function _getMoreSelectQuery(): string
    {
        return '';
    }

    /**
     * Getting more From query part
     *
     * @return string
     */
    protected function _getMoreFromQuery(): string
    {
        return '';
    }

    /**
     * Getting Join query part
     *
     * @return string
     */
    protected function _getJoinQuery(): string
    {
        return '';
    }

    /**
     * Getting more WHERE query part
     *
     * @return string
     */
    protected function _getMoreWhereQuery(): string
    {
        return '';
    }

    /**
     * Getting conditions query part and binded params
     *
     * @return array
     */
    protected function _getConditionsQuery(): array
    {
        $query = 'WHERE 1 = 1';
        $bindedParams = [];

        if (!empty($this->_conditions)) {
            foreach ($this->_conditions as $param => $value) {
                unset($logic, $sign, $bindedValue);
                if (is_array($value)) {
                    if (!empty($value['logic'])) {
                        $logic = $value['logic'];
                    }
                    if (!empty($value['sign'])) {
                        $sign = $value['sign'];
                    }
                    if (!empty($value['value'])) {
                        $bindedValue = $value['value'];
                    }
                }

                if (!isset($logic)) {
                    $logic = 'AND';
                }
                if (!isset($sign)) {
                    $sign = '=';
                }
                if (!isset($bindedValue)) {
                    $bindedValue = $value;
                }

                $query .= ' ' . $logic . ' ' . $param .= ' ' . $sign . ' ?';
                $bindedParams[':' . $param] = $bindedValue;
            }
        }

        $query .= $this->_getMoreWhereQuery();

        return ['query' => $query, 'bindedParams' => $bindedParams];
    }

    /**
     * Getting order query part
     *
     * @return string
     */
    protected function _getOrderQuery(): string
    {
        if (empty($this->_order)) {
            return '';
        }

        $order = '';
        foreach ($this->_order as $param => $type) {
            if ($order !== '') {
                $order .= ', ';
            }
            $order .= $param . ' ' . $type;
        }

        return 'ORDER BY ' . $order;
    }

    /**
     * Getting Limit query part
     *
     * @return string
     */
    protected function _getLimitQuery(): string
    {
        if (is_null($this->_size)) {
            return '';
        }
        $offset = ($this->_page - 1) * $this->_size;
        return 'LIMIT ' . $offset . ', ' . $this->_size;
    }
}