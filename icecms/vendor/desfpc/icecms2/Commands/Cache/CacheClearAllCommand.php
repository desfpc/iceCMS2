<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Console app Class
 */

namespace iceCMS2\Commands\Cache;

use iceCMS2\Caching\CachingFactory;
use iceCMS2\Commands\CommandInterface;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class CacheClearAllCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'cache-clear-all - Clear all DB caches.';

    /**
     *
     * @param Settings $settings
     * @param array|null $param *
     *
     * @return string
     * @throws Exception
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        $result = "\n" . 'IceCMS2 Clear all caches';
        $cache = CachingFactory::instance($settings);
        $keys = $cache->findKeys($settings->db->name . '*');
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $result .= "\n" . $key . ' ';
                if ($cache->del($key)) {
                    $result .= "\e[32m" . '[DELETED]' . "\e[39m";
                } else {
                    $result .= "\e[31m" . '[ERROR]' . "\e[39m";
                }
            }
        }

        return $result;
    }
}