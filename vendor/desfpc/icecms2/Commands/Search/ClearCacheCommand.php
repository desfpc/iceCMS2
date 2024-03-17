<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Elastic command class
 */

namespace iceCMS2\Commands\Search;

use Exception;
use iceCMS2\Commands\CommandInterface;
use iceCMS2\Search\SearchFactory;
use iceCMS2\Settings\Settings;

final class ClearCacheCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'clear-cache - Clear search cache';

    /**
     * @throws Exception
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        $params['index'] = '_cache/clear';
        $params['method'] = 'POST';
        return SearchFactory::instance($settings)->clearCache($settings, $params);
    }
}