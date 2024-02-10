<?php

namespace app\commands;

use iceCMS2\Commands\CommandInterface;
use iceCMS2\Settings\Settings;

class AMyCommand implements CommandInterface
{
    /** @var string  */
    public string $info = 'a-my -- test command';

    /**
     * @param Settings $settings
     * @param array|null $param
     *
     * @return string
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        return 'test command';
    }
}