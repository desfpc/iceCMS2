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
     * Generate random string
     *
     * @param int $len
     * @param string|null $alphabet
     * @return string
     */
    public static function getRandomString(int $len = 8, ?string $alphabet = null): string
    {
        if (is_null($alphabet)) {
            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()_-+=?';
        }
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $len; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    /**
     * Generate random password
     *
     * @param int $minLen
     * @return string
     */
    public function getRandomPassword(int $minLen = 8): string
    {
        $pas = self::getRandomString($minLen);
        $errorCode = self::getPassError($pas);

        if ($errorCode === 2) {
            $pas = $pas . self::getRandomString(1, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
            $errorCode = self::getPassError($pas);
        }
        if ($errorCode === 3) {
            $pas = $pas . self::getRandomString(1, 'abcdefghijklmnopqrstuvwxyz');
            $errorCode = self::getPassError($pas);
        }
        if ($errorCode === 4) {
            $pas = $pas . self::getRandomString(1, '1234567890');
        }

        return $pas;
    }

    /**
     * Check password is secure and get error code (0 - if no error and password is secure)
     *
     * @param string $text
     * @return int
     */
    public static function getPassError(string $text): int
    {
        if (mb_strlen($text, 'UTF-8') < 8) {
            return 1;
        }
        if (!preg_match('@[A-ZА-ЯЁ]@', $text)) {
            return 2;
        }
        if (!preg_match('@[a-zа-яё]@', $text)) {
            return 3;
        }
        if (!preg_match('@[0-9]@', $text)) {
            return 4;
        }
        return 0;
    }

    /**
     * Text transliterate
     *
     * @param string $string
     * @param string $language
     * @return string
     */
    public static function transliterate(string $string, string $language): string
    {
        return match ($language) {
            'Ru' => self::transliterateRu($string),
            default => $string,
        };
    }

    /**
     * Text transliterate from Ru to En
     *
     * @param string $string
     * @return string
     */
    public static function transliterateRu(string $string): string
    {
        $table = array(
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
            'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH',
            'Ш' => 'SH', 'Щ' => 'SCH', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',

            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        );

        $output = str_replace(
            array_keys($table),
            array_values($table), $string
        );

        $output = preg_replace('/[^-a-z0-9._\[\]\'"]/i', ' ', $output);
        $output = preg_replace('/ +/', '-', $output);

        return $output;
    }

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