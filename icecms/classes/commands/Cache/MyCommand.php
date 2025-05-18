<?php

namespace app\commands\Cache;

use iceCMS2\Commands\CommandInterface;
use iceCMS2\Settings\Settings;

class MyCommand implements CommandInterface
{
    /** @var string  */
    public string $info = 'my –– test command';

    /**
     * @param Settings $settings
     * @param array|null $param
     *
     * @return string
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        return "test";
    }
}