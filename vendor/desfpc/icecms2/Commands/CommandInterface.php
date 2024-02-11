<?php

namespace iceCMS2\Commands;

use iceCMS2\Settings\Settings;

interface CommandInterface
{
    public static function run(Settings $settings, ?array $param = null): string;
}