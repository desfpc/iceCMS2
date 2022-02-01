<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Strings Helpers
 */

namespace iceCMS2\Helpers;

use iceCMS2\Settings\Settings;

class Strings
{
    /**
     * Convert CamelCase string to snake_case
     *
     * @param string $camel string in CamelCase or lowerCamelCase
     * @return string string in snake_case
     */
    public static function camelToSnake(string $camel): string
    {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $camel)), '_');
    }

    /**
     * @param string $snake string in snake_case
     * @param bool $isLowerCamelCase loverCanelCase flag
     * @return string string in CamelCase or lowerCamelCase
     */
    public static function snakeToCamel(string $snake, bool $isLowerCamelCase = true): string
    {
        $camel = str_replace(['_','-'], '', ucwords($snake, '_-'));
        if ($isLowerCamelCase) {
            $camel = lcfirst($camel);
        }
        return $camel;
    }

    /**
     * Getting cache key with site name
     *
     * @param Settings $settings
     * @param string $key
     * @return string
     */
    public static function cacheKey(Settings $settings, string $key): string
    {
        return $settings->site->primaryDomain . '_' . $key;
    }
}