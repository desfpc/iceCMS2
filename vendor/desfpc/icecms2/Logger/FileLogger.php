<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * File Logger class
 */

namespace iceCMS2\Logger;

use iceCMS2\Helpers\LoggerHelper;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class FileLogger implements LoggerInterface
{
    use LoggerHelper;

    /** @var string */
    private const PATH = __DIR__ . '/../../../../logs';

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function log(string $type, mixed $data, Settings $settings): bool
    {
        $logFile = $settings->path . '/logs/' . $type . match ($settings->logs->period) {
                'day' => '_' . date('Y-m-d') . '.log',
                'month' => '_' . date('Y-m') . '.log',
                'year' => '_' . date('Y') . '.log',
                default => '.log',
            };

        $data = self::convertDataToString($data);

        $logData = date('Y-m-d H:i:s') . ' ' . $data . PHP_EOL;
        return (bool)file_put_contents($logFile, $logData, FILE_APPEND);
    }

    /**
     * @param Settings $settings
     *
     * @return bool
     */
    public function clearOnPeriodLogs(Settings $settings): bool
    {
        $period = isset($settings->logs->periodClear) ? $settings->logs->periodClear : 'month';

        $periodData = match ($period) {
            'day' => date('Y-m-d'),
            'month' => date('Y-m'),
            'year' => date('Y'),
            default => date('Y-m-d')
        };

        $rmFiles = self::gerFileName($period, $periodData);

        $i = 0;
        foreach ($rmFiles as $rmFile) {
            $filePath = self::PATH . '/' . $rmFile;

            if (file_exists($filePath)) {
                unlink($filePath);
                $i++;
            }
        }
        return 0 === $i ? false : true;
    }

    /**
     * @param Settings $settings
     *
     * @return bool
     */
    public function clearAllLogs(Settings $settings): bool
    {
        $files = glob(self::PATH . '/*');
        $i = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $i++;
            }
        }
        return 0 === $i ? false : true;
    }

    /**
     * @param string $period
     * @param string $periodData
     *
     * @return array
     */
    private static function gerFileName(string $period, string $periodData): array
    {
        $dir = dir(self::PATH);

        $rmFile = [];
        while (false !== ($file = $dir->read())) {
            if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                continue;
            }

            $filesTmp = explode('_', $file);
            if (isset($filesTmp[1])) {
                $filesTmp = str_replace('.log', '', $filesTmp[1]);
                if (strpos($filesTmp, $periodData) !== false) {
                    $rmFile[] = $file;
                }
            }
        }

        return $rmFile;
    }
}