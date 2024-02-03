<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Strings Helpers
 */

namespace iceCMS2\Helpers;

use iceCMS2\Tools\Exception;

trait LoggerHelper
{
    /**
     * @param mixed $data
     *
     * @return string
     * @throws Exception
     */
    public static function convertDataToString(mixed $data): string
    {
        switch (gettype($data)) {
            case 'array':
                $data = json_encode($data);
                break;
            case 'object':
                $data = json_encode($data->__serialize());
                break;
            case 'string':
                break;
            default:
                throw new Exception('FileLogger::log() - unknown data type ' . gettype($data));
        }

        if(!$data){
            $data = '';
        }

        return $data;
    }
}