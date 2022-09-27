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

use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class FileLogger implements LoggerInterface
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function log(Settings $settings, string $type, mixed $data): bool
    {
        $logFile = $settings->logs->path . '/' . $type . match ($settings->logs->period) {
            'day' => '_' . date('Y-m-d') . '.log',
            'month' => '_' . date('Y-m') . '.log',
            'year' => '_' . date('Y') . '.log',
            default => '.log',
        };

        switch (gettype($data)) {
            case 'array':
                $data = json_encode($data);
                break;
            case 'string':
                break;
            default:
                throw new Exception('FileLogger::log() - unknown data type ' . gettype($data));
        }

        $logData = date('Y-m-d H:i:s') . ' ' . $data . PHP_EOL;
        return file_put_contents($logFile, $logData, FILE_APPEND);
    }
}