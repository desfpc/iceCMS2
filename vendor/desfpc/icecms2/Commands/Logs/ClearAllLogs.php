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

use iceCMS2\Models\User;

final class ClearAllLogs
{
    /** @var string  */
    private const PATH = '../logs';

    /**
     * @return bool
     */
    public static function main(): bool
    {
        $dir = dir(self::PATH);
        $i = 0;
        $files = glob('../logs/*.log');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $i++;
            }
        }

        return 0 === $i ? false : true;
    }
}