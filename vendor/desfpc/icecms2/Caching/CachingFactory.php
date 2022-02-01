<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Caching Factory
 */

namespace iceCMS2\Caching;

use iceCMS2\Settings\Settings;

class CachingFactory
{
    /**
     * Getting a cache object
     *
     * @param Settings $settings
     * @return CachingInterface
     * @throws \Exception
     */
    public static function instance(Settings $settings): CachingInterface
    {
        if ($settings->cache->useRedis) {
            return new Redis($settings->cache->redisHost, $settings->cache->redisPort);
        } else {
            throw new \Exception('Redis caching is not configured, but the file cache is not done yet ...');
        }
    }
}