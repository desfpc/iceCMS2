<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Logger Factory
 */

namespace iceCMS2\Logger;

use iceCMS2\Settings\Settings;

class LoggerFactory
{
    /**
     * Get a logger instance
     *
     * @param Settings $settings
     * @return LoggerInterface
     */
    public static function instance(Settings $settings): LoggerInterface
    {
        return match ($settings->logs->type) {
            'db' => new DBLogger(),
            default => new FileLogger()
        };
    }
}