<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Strings Helpers
 */

namespace iceCMS2\Commands\Logs;

use iceCMS2\Logger\DBLogger;
use iceCMS2\Logger\FileLogger;
use iceCMS2\Models\User;
use iceCMS2\Settings\Settings;

final class ClearLogs
{
    /** @var string */
    private const PATH = __DIR__ . '/../../../../../settings/settingsSelector.php';

    /**
     * @param Settings $settings
     *
     * @return DBLogger|FileLogger
     */
    public static function instance(Settings $settings): DBLogger|FileLogger
    {
        return match ($settings->logs->type) {
            'db' => new DBLogger(),
            default => new FileLogger()
        };
    }

    /**
     * @param string|null $parametr
     *
     * @return array|bool|int
     * @throws Exception
     */
    public static function clearAllLogs(): array|bool|int
    {
        [$logger,$settings] = self::getInstans();
        return $logger->clearAllLogs($settings);
    }

    /**
     * @param string|null $parametr
     *
     * @return array|bool|int
     * @throws Exception
     */
    public static function clearOnPeriodLogs(): array|bool|int
    {
        [$logger,$settings] = self::getInstans();
        return $logger->clearOnPeriodLogs($settings);
    }

    private static function getInstans()
    {
        /** @var array $settings Settings array from settingsSelector.php */
        require self::PATH;

        $settings = new Settings($settings);
        $logger = self::instance($settings);
        return [$logger,$settings];
    }
}