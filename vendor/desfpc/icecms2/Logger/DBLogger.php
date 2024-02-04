<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * DB Logger class
 */

namespace iceCMS2\Logger;

use iceCMS2\DB\DBFactory;
use iceCMS2\DB\DBInterface;
use iceCMS2\Models\AbstractEntity;
use iceCMS2\Models\Log;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;
use iceCMS2\Helpers\LoggerHelper;

class DBLogger implements LoggerInterface
{
    use LoggerHelper;

    /**
     * @inheritDoc
     * @throws Exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public static function log(string $type, mixed $data, Settings $settings): bool
    {
        if ($data instanceof AbstractLogEntity) {
            return $data->save();
        } elseif (is_array($data)) {
            $fullClassName = 'iceCMS2\Models\Log';
            /** @var AbstractEntity $log */
            $log = new $fullClassName($settings);

            $data = self::convertDataToString($data);

            $log->set([
                'alias' => $type,
                'value' => $data,
            ]);

            return $log->save();
        }

        throw new Exception('DBLogger::log() - unknown data type ' . gettype($data));
    }

    /**
     * @param Settings $settings
     *
     * @return array|bool|int
     * @throws Exception
     */
    public static function clearOnPeriodLogs(Settings $settings): array|bool|int
    {
        $db = self::getDDFactory($settings);

        $query = match ($settings->logs->periodClear) {
            'day' => [
                'qyery' => 'DELETE FROM logs WHERE DATE(created_time) = ?',
                'param' => [date('Y-m-d')]
            ],
            'month' => [
                'qyery' => 'DELETE FROM logs WHERE YEAR(created_time) = ? AND MONTH(created_time) = ?',
                'param' => [date('Y'), date('m')]
            ],
            'year' => [
                'qyery' => 'DELETE FROM logs WHERE YEAR(created_time) = ?',
                'param' => [date('Y')]
            ],
        };

        return $db->queryBinded($query['qyery'], $query['param']);
    }

    /**
     * @param Settings $settings
     *
     * @return array|bool|int
     * @throws Exception
     */
    public static function clearAllLogs(Settings $settings): array|bool|int
    {
        $db = self::getDDFactory($settings);
        return $db->query('TRUNCATE TABLE logs');
    }

    /**
     * @param Settings $settings
     *
     * @return DBInterface|null
     * @throws Exception
     */
    private static function getDDFactory(Settings $settings): ?DBInterface
    {
        $db = (new DBFactory($settings))->db;
        $db->connect();
        return $db;
    }
}