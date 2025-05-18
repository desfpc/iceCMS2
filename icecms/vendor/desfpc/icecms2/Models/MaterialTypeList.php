<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Material Types entity list class
 */

namespace iceCMS2\Models;

use iceCMS2\Caching\CachingFactory;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class MaterialTypeList extends AbstractEntityList
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'material_type';

    /** @var array|null Material Types tree array */
    public static ?array $tree = null;

    /** @var int Tree cache expire time */
    private const TREE_CACHE_EXPIRE = 3600;

    /**
     * Getting more select query part
     *
     * @return string
     */
    protected function _getMoreSelectQuery(): string
    {
        return ', `pmt`.`name` `parent_name`' .
            ', `mtp1`.`name` `template_list_name`' .
            ', `mtp2`.`name` `template_material_name`' .
            ', `mtp3`.`name` `template_admin_name`';
    }

    /**
     * Getting Join query part
     *
     * @return string
     */
    protected function _getJoinQuery(): string
    {
        return ' LEFT JOIN `material_types` `pmt` ON `dbtable`.`parent_id` = `pmt`.`id` ' .
            ' LEFT JOIN `material_templates` `mtp1` ON `dbtable`.`template_list` = `mtp1`.`id` ' .
            ' LEFT JOIN `material_templates` `mtp2` ON `dbtable`.`template_material` = `mtp2`.`id` ' .
            ' LEFT JOIN `material_templates` `mtp3` ON `dbtable`.`template_admin` = `mtp3`.`id` ';
    }

    /**
     * Get cache key for tree
     *
     * @param Settings $settings
     * @param string $mode
     * @return string
     */
    private static function getTreeCacheKey(Settings $settings, string $mode = 'all'): string
    {
        return $settings->db->name . '_material_types_tree_' . $mode;
    }

    /**
     * Get material types tree
     *
     * @param Settings $settings
     * @param string $mode - "all" types, "menu" in menu types, "mat" types with active materials,
     * "menumat" active in menu types with active materials
     * @return array
     * @throws Exception
     */
    public static function getTree(Settings $settings, string $mode = 'all'): array
    {
        $tree = [];

        $cacher = CachingFactory::instance($settings);
        $key = self::getTreeCacheKey($settings, $mode);
        if ($cacher->has($key)) {
            $tree = $cacher->get($key);
        } else {
            if (empty(self::$tree[$mode])) {
                $conditions = match ($mode) {
                    'menu' => [
                        'sitemenu' => 1,
                    ],
                    'mat' => [
                        'id' => [
                            'logic' => 'AND',
                            'sign' => 'IN',
                            'value' => '(SELECT material_type_id FROM materials WHERE status_id = 2)',
                        ],
                    ],
                    'menumat' => [
                        'sitemenu' => 1,
                        'id' => [
                            'logic' => 'AND',
                            'sign' => 'IN',
                            'value' => '(SELECT material_type_id FROM materials WHERE status_id = 2)',
                        ],
                    ],
                    default => [],
                };

                $order = [
                    'parent_id' => 'ASC',
                    'ordernum' => 'ASC',
                    'id' => 'ASC',
                ];

                $typesList = new self($settings, $conditions, $order);
                if ($rows = $typesList->get() && !empty($rows)) {
                    /** @var array $rows */
                    foreach ($rows as $row) {
                        if (is_null($row['parent_id'])) {
                            $row['parent_id'] = 'null';
                        }

                        $tree['types'][$row['id']] = $row;
                        $tree['childs'][$row['parent_id']][$row['id']] = $row;
                    }
                }

                self::$tree[$mode] = $tree;
            } else {
                $tree = self::$tree[$mode];
            }

            $cacher->set($key, $tree, self::TREE_CACHE_EXPIRE);
        }

        return $tree;
    }
}