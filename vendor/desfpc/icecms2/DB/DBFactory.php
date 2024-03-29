<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * DB Factory
 */

namespace iceCMS2\DB;

use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class DBFactory
{
    /**
     * For single DB connection usage
     *
     * @var DBInterface|null
     */
    private static ?DBInterface $_singleDB = null;

    /**
     * For many DB connections usage
     *
     * @var ?DBInterface
     */
    public ?DBInterface $db = null;

    /**
     * Class Constructor for new connections
     *
     * @param Settings $settings
     * @throws Exception
     */
    public function __construct(Settings $settings)
    {
        $this->db = match ($settings->db->type) {
            'MySQL' => new MySql($settings->db),
            default => throw new Exception("Unknown DB type " . $settings->db->type),
        };
    }

    /**
     * Class Constructor for singleton
     *
     * @param Settings $settings
     * @return DBInterface|null
     * @throws Exception
     */
    public static function get(Settings $settings): ?DBInterface
    {
        if (is_null(self::$_singleDB)) {
            $connection = new self($settings);
            self::$_singleDB = $connection->db;
        }
        return self::$_singleDB;
    }

    /**
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a connection.");
    }

    private function __clone()
    {
    }
}