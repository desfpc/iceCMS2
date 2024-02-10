<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Console app Class
 */

namespace iceCMS2\Commands\SymLink;

use iceCMS2\Commands\CommandInterface;
use iceCMS2\Settings\Settings;

class MakeSymlinksCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'make-symlinks - Make symlinks from vendor to project folders.';

    /**
     *
     * @param Settings $settings
     * @param array|null $param *
     *
     * @return string
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        $result = "\n" . 'IceCMS2 Make symlinks';

        $symlinks = [
            'vendor/desfpc/vuebootstrap/src' => 'web/js/vuebootstrap',
            'vendor/desfpc/icecms2/Web/js' => 'web/js/vendor',
            'vendor/desfpc/icecms2/Templates/ice' => 'templates/vendor/ice',
            'vendor/desfpc/icecms2/Controller/vendor' => 'controllers/vendor'
        ];

        foreach ($symlinks as $key => $value) {
            $result .= "\n";

            if (file_exists($settings->path . $value)) {
                unlink($settings->path . $value);
            }

            if (symlink($settings->path . $key, $settings->path . $value)) {
                $result .= "\e[32m" . $value . ' - [OK]' . "\e[39m";
            } else {
                $result .= "\e[31m" . $value . ' - [ERROR] (' . $settings->path . $key . ' -> '
                    . $settings->path . $value . ')' . "\e[39m";
            }
        }
        $result .= "\n";

        return $result;
    }
}