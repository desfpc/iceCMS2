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

class MigrationCreateCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'migration-create {name} - Create blank new DB migration with name {name}. Name must be in CamelCase.';

    /**
     * @param Settings $settings
     * @param array|null $param
     *
     * @return string
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        $result = "\n" . 'IceCMS2 Migration Creating';
        if (empty($param[2])) {
            $param[2] = null;
        }
        $migrations = new Migrations($settings);
        if (!$migrations->create($param[2])) {
            $result .= "\n\e[31m" . 'Error when trying create migration: ' . $migrations->getError() . "\e[39m";
        } else {
            $result .= "\n\e[32m" . 'Migration created!' . "\e[39m";
        }
        $result .= "\n\n";

        return $result;
    }
}