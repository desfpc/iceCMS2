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

use iceCMS2\Commands\CommandInterface;
use iceCMS2\Settings\Settings;

class PhpCacheClearCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'php-cache-clear - Clear PHP caches.';

    /**
     *
     * @param Settings $settings
     * @param array|null $param *
     *
     * @return string
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        $result = "\n" . 'IceCMS2 Clear PHP caches';
        opcache_reset();
        $result .= "\n\e[32m" . 'OPCaches cleared!' . "\e[39m";
        clearstatcache();
        $result .= "\n\e[32m" . 'Stat caches cleared!' . "\e[39m" . PHP_EOL;

        return $result;
    }
}