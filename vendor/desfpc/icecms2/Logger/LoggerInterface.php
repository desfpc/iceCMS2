<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Logger interface
 */

namespace iceCMS2\Logger;

use iceCMS2\Settings\Settings;

interface LoggerInterface
{
    /**
     * Log some data
     *
     * @param Settings $settings
     * @param string $type
     * @param mixed $data
     * @return bool
     */
    public static function log(Settings $settings, string $type, mixed $data): bool;
}