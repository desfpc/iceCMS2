<?php

namespace iceCMS2\Commands\Queue;

use iceCMS2\Tools\Exception;
use iceCMS2\Commands\CommandInterface;
use iceCMS2\Queue\QueueFactory;
use iceCMS2\Settings\Settings;
use RedisException;

class RunQueueCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'run-queue - Start the oldest task from the NameQueue. Example: run-queue NameQueue';

    /**
     * @param Settings $settings
     * @param array|null $param
     *
     * @return string
     * @throws RedisException
     * @throws Exception
     */
    public static function run(Settings $settings, ?array $param = null): string
    {
        if (!isset($param[2])) {
            $result = 'Queue name is required. Example: run-queue NameQueue';
        } else {
            $result = json_encode(
                QueueFactory::queue()->runQueue($param[2]), true
            );
        }

        return $result;
    }
}