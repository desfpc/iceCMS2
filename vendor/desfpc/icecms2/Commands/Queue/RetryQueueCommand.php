<?php

namespace iceCMS2\Commands\Queue;

use iceCMS2\Commands\CommandInterface;
use iceCMS2\Queue\QueueFactory;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;
use RedisException;

class RetryQueueCommand implements CommandInterface
{
    /** @var string */
    public string $info = 'retry-queue - Restart all failed tasks from NameQueue or a NameQueue and specific task. Example: retry-queue NameQueue all OR retry-queue NameQueue NameTask';

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
        $result = [];
        $failedTasks = QueueFactory::queue()->getFailedTasks();

        foreach ($failedTasks as $taskId => $taskData) {
            $taskData = json_decode($taskData, true);
            QueueFactory::queue()->create('failed_retru', $taskData);
            QueueFactory::queue()->deleteTask($taskId);
            $result[] = QueueFactory::queue()->runQueue('failed_retru');
        }

        return json_encode($result, true);
    }
}