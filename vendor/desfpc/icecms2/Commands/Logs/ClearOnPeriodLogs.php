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
use iceCMS2\Logger\LoggerInterface;
use iceCMS2\Models\User;
use iceCMS2\Settings\Settings;

final class ClearOnPeriodLogs
{
    /** @var string */
    private const PATH = __DIR__.'/../../../../../logs';

    public static function instance(): LoggerInterface
    {
        var_dump($settings);
        return match ($settings->logs->type) {
            'db' => new DBLogger(),
            default => new FileLogger()
        };
    }
// !!!!!!! не забыть про тест если хранится все в БД!!!!! фабрика

    public static function main(?string $parametr = null)
    {
        /** @var array $settings Settings array from settingsSelector.php */
        require __DIR__ . '/../../../../../settings/settingsSelector.php';

        $period = isset($settings['logs']['period_clear']) ? $settings['logs']['period_clear'] : 'month';

        $periodData = match (is_null($parametr) ? $period : $parametr) {
            'day' => date('Y-m-d'),
            'month' => date('Y-m'),
            'year' => date('Y'),
            default => date('Y-m-d')
        };

        $rmFile = self::gerFileName($period, $periodData);

echo "<pre>";
print_r($rmFile);

            //  должен полуячить имя файла который нужно удалить
        return true;
    }

    /**
     * @param string $period
     * @param string $periodData
     *
     * @return array
     */
    private static function gerFileName(string $period, string $periodData)
    {
        $dir = dir(self::PATH);

        $rmFile = [];
        while (false !== ($file = $dir->read())) {
            if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                continue;
            }

            $filesTmp = explode('_', $file);
            if(isset($filesTmp[1])){
                $filesTmp = str_replace('.log', '', $filesTmp[1]);
                if (strpos($filesTmp, $periodData) !== false) {
                    $rmFile[] = $file;
                }
            }
        }

        return $rmFile;
    }
}