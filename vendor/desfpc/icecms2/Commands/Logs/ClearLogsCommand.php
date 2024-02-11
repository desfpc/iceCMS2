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

use iceCMS2\Commands\CommandInterface;
use iceCMS2\Logger\DBLogger;
use iceCMS2\Logger\FileLogger;
use iceCMS2\Settings\Settings;

final class ClearLogsCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'clear-logs - Delete all log or period log files or DB. Example: clear-logs all OR clear-logs period d|m|y';

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
     * @return array|bool|int
     */
    public static function clearAllLogs(): array|bool|int
    {
        [$logger, $settings] = self::getInstans();
        return $logger::clearAllLogs($settings);
    }

    /**
     * @param string|null $period
     *
     * @return array|bool|int
     */
    public static function clearOnPeriodLogs(?string $period = null): array|bool|int
    {
        [$logger, $settings] = self::getInstans();
        return $logger::clearOnPeriodLogs($settings, $period);
    }

    /**
     * @return array
     */
    private static function getInstans(): array
    {
        /** @var array $settings Settings array from settingsSelector.php */
        require self::PATH;

        $settings = new Settings($settings);
        $logger = self::instance($settings);
        return [$logger, $settings];
    }

    /**
     * @param Settings $settings
     * @param array|null $param
     *
     * @return string
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        $params = ['d', 'm', 'y',];
        $result = "\n" . 'IceCMS2 Delete all or period log files or DB. Example: clear-logs all OR clear-logs period d|m|y';

        if ($param && $param[2] === 'all') {
            self::clearAllLogs();
            $result .= "\n\e[32m" . 'Cleared all logs!' . "\e[39m" . PHP_EOL;
        }

        if ($param && $param[2] === 'period' && isset($param[3]) && in_array($param[3], $params)) {
            self::clearOnPeriodLogs($param[3]);
            $result .= "\n\e[32m" . "Cleared in period $param[3] logs!" . "\e[39m" . PHP_EOL;
        } elseif ($param && $param[2] === 'period' && isset($param[3]) && !in_array($param[3], $params)) {
            $result = "\n" . 'Sorry, but IceCMS2 Delete all or period log files. Example: clear-logs all OR clear-logs period d|m|y';
        }

        if ($param && $param[2] === 'period' && !isset($param[3])) {
            self::clearOnPeriodLogs();
            $result .= "\n\e[32m" . "Cleared in period logs!" . "\e[39m" . PHP_EOL;
        }
        return $result;
    }
}