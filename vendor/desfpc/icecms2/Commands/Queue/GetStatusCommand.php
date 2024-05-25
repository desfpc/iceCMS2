<?php

namespace iceCMS2\Commands\Queue;

use iceCMS2\Commands\CommandInterface;
use iceCMS2\Queue\QueueFactory;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;
use RedisException;

class GetStatusCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'get-status-queue - Get how many tasks with a specific status from a all queues. Example: get-status-queue NameStatus. Example - in process|completed|failed';

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
        if(isset($param[2]) && $param[2] === 'in' && $param[3] === 'process'){
            $param[2] = 'in process';
        }
        return json_encode(
            QueueFactory::queue()->getTasksByStatus($param[2] ?? 'in process'), true
        );
    }
}