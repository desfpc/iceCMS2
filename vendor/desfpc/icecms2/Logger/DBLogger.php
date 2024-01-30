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

use iceCMS2\Models\AbstractEntity;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class DBLogger implements LoggerInterface
{
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
            $log->set([
                'alias' => $type,
                'value' => $data,
                'created_time' => time(),
                'updated_time' => time(),
            ]);
            return $log->save();
        }

        throw new Exception('DBLogger::log() - unknown data type ' . gettype($data));
    }
}