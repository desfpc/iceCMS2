<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * LocaleText class
 */

namespace iceCMS2\Locale;

use iceCMS2\Caching\CachingFactory;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class LocaleText
{
    /**
     * Get locale text by key and language
     *
     * @param Settings $settings
     * @param string $key
     * @param array|null $values
     * @param string|null $locale
     * @param bool $arrayReturn
     * @return string|array
     * @throws Exception
     */
    public static function get(Settings $settings, string $key, ?array $values = null, ?string $locale = null, $arrayReturn = false): string|array
    {
        if (is_null($locale)) {
            $locale = $settings->locale;
        }

        $key = str_replace('\\/', '\_', $key);

        $cacher = CachingFactory::instance($settings);
        $textCacheKey = self::_getTextCacheKey($settings, $key, $values, $locale);
        if ($cacher->has($textCacheKey)) {
            return $cacher->get($textCacheKey);
        }

        $keyArr = explode('/', $key);

        $template = $keyArr[count($keyArr) - 1];
        $lastArray = $keyArr;

        if (in_array($locale, $settings->locales)) {
            $patch = $settings->path . 'locale/' . $locale . '/' . $keyArr[0] . '.php';
            unset($keyArr[0]);

            if (is_file($patch)) {
                $fileCacheKey = self::_getFileCacheKey($settings, $patch);
                if ($cacher->has($fileCacheKey)) {
                    $langArr = $cacher->get($fileCacheKey, true);
                } else {
                    include_once($patch);
                    if (!isset($langArr)) {
                        $langArr = [];
                    }
                    $cacher->set($fileCacheKey, json_encode($langArr), 60);
                }

                if (!empty($langArr)) {

                    foreach ($keyArr as $key) {
                        $key = str_replace('\_', '/', $key);
                        if (isset($langArr[$key])) {
                            $langArr = $langArr[$key];
                            if (!is_array($langArr)) {
                                $template = $langArr;
                                break;
                            } else {
                                $lastArray = $langArr;
                            }
                        } else {
                            break;
                        }
                    }
                }
            } elseif ($locale !== 'en') {
                throw new Exception('Locale file not found: ' . $patch);
            }
        }

        if ($arrayReturn) {
            return $lastArray;
        }

        if (!empty($values)) {
            foreach ($values as $key => $value) {
                $template = str_replace('{' . $key . '}', $value, $template);
            }
        }

        $cacher->set($textCacheKey, $template, 360);
        return $template;
    }

    /**
     * Get cache key for file array
     *
     * @param Settings $settings
     * @param string $patch
     * @return string
     */
    private static function _getFileCacheKey(Settings $settings, string $patch): string
    {
        return $settings->db->name . '_localeFile_' . $patch;
    }

    /**
     * Get cache key for locale text
     *
     * @param Settings $settings
     * @param string $key
     * @param array|null $values
     * @param string $locale
     * @return string
     */
    private static function _getTextCacheKey(Settings $settings, string $key, ?array $values, string $locale): string
    {
        return $settings->db->name . '_localeText_' . $key . '_' . json_encode($values) . '_' . $locale;
    }
}