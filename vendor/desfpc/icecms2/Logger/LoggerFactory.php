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
use iceCMS2\Tools\FlashVars;

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

    /**
     * @param Settings $settings
     * @param string $nameFileLog
     * @param mixed $date
     *
     * @return void
     */
    public static function log(string $nameFileLog, mixed $date, ?Settings $settings = null): bool
    {
        if (strpos($nameFileLog, "_") !== false) {
            $flashVars = new FlashVars();
            $flashVars->set('error', 'you cannot use _ in the file name');
            return false;
        }

        if(is_null($settings)){
            /** @var array $settings Settings array from settingsSelector.php */
            require __DIR__ . '/../../../../settings/settingsSelector.php';

            $settings = new Settings($settings);
        }

        $logger = self::instance($settings);
        $result = $logger::log($nameFileLog, $date, $settings);

        return $result;
    }
}