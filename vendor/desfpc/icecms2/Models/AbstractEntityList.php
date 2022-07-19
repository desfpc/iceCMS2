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

    /** @var int Query results page */
    protected int $_page = 1;

    /** @var int Query results count (page size) */
    protected int $_size = 10;

    /**
     * Entity list constructor
     *
     * @param Settings $settings
     * @param string $dtable
     * @param array $conditions
     * @param array $order
     * @param int $page
     * @param int $size
     */
    public function __construct(Settings $settings, string $dtable, array $conditions = [], array $order = [], int $page = 1, int $size = 10)
    {
        $this->_settings = $settings;
        $this->_db = DBFactory::get($this->_settings);
        $this->_dbtable = $dtable;
        $this->_conditions = $conditions;
        $this->_order = $order;
        $this->_page = $page;
        $this->_size = $size;
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
            return $res[0]['cnt'];
        }
        return false;
    }

    /**
     * Getting array of entityes
     *
     * @return array
     */
    public function get(): array
    {
        [$query, $bindedParams] = $this->_getFullQuery();
        return $this->_db->queryBinded($query, $bindedParams);
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

        if ($ifCnt) {
            $query .= ' ' . $this->_getOrderQuery() . ' ' . $this->_getLimitQuery();
        }

        return ['query' => $query, 'bindedParams' => $bindedParams];
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
            $query = 'SELECT COUNT(`dbtable`.`id`) `cnt` ';
        } else {
            $query = 'SELECT `dbtable`.* ';
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
                $query .= ' AND ' . $param .= ' = ?';
                $bindedParams[':' . $param] = $value;
            }
        }

        return ['query' => $query, 'bindedParams' => $bindedParams];
    }

    /**
     * Getting order query part
     *
     * @return string
     */
    protected function _getOrderQuery(): string
    {
        $order = '';

        if (!empty($this->_order)) {
            foreach ($this->_order as $param => $type) {
                if ($order !== '') {
                    $order .= ', ';
                }
                $order .= $param . ' ' . $type;
            }
        }

        return $order;
    }

    /**
     * Getting Limit query part
     *
     * @return string
     */
    protected function _getLimitQuery(): string
    {
        $offset = ($this->_page - 1) * $this->_size;
        return 'LIMIT ' . $offset . ' ' . $this->_size;
    }
}