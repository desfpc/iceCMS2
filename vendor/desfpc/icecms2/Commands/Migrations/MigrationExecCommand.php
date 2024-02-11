<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Console app Class
 */

namespace iceCMS2\Commands\Migrations;

use iceCMS2\Cli\Migrations;
use iceCMS2\Commands\CommandInterface;
use iceCMS2\Settings\Settings;

class MigrationExecCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'migration-exec - Execute DB migrations.';

    /**
     * @param Settings $settings
     * @param array|null $param
     *
     * @return string
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        $result = "\n" . 'IceCMS2 Migration Executing';
        $migrations = new Migrations($settings);
        if (!$migrations->exec()) {
            $result .= "\n\e[31m" . 'Error when trying execute migrations: ' . $migrations->getError() . "\e[39m";
        } else {
            $result .= "\n\e[32m" . 'Migrations executed!' . "\e[39m";
        }
        $result .= "\n\n";

        return $result;
    }
}