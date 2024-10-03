<?php

namespace iceCMS2\Queue;

use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;
use Redis as Rediska;
use RedisException;
use Throwable;

class RedisQueue implements QueueInterface
{
    /** @var ?Rediska $redis Redis client object */
    private ?Rediska $redis = null;

    /** @var array Queue errors array */
    public array $errors = [];

    /**
     * @throws Exception
     */
    public function __construct()
    {
        try {
            $host = $this->_getSettings()->queue->redis['host'];
            $port = $this->_getSettings()->queue->redis['redisPort'];
            $db = $this->_getSettings()->queue->redis['redisDB'];

            $this->redis = new Rediska();
            if (!$this->redis->connect($host, $port)) {
                $this->errors[] = 'Redis connection error';
            }

            if (!is_null($db)) {
                $this->redis->select($db);
            }
        } catch (Throwable $e) {
            $this->errors[] = $e->getMessage();
        }

        if (!empty($this->errors)) {
            throw new Exception('Error: ' . implode(', ', $this->errors));
        }
    }

    /**
     * @param string $queue
     * @param array $params
     *
     * @return bool
     * @throws RedisException
     */
    public function create(string $queue, array $params): bool
    {
        //Adding a task to the queue
        $taskId = uniqid();
        $this->redis->lPush($queue, json_encode($params + ['task_id' => $taskId]));

        $statusInfo = [
            'status' => 'in process',
            'timestamp' => time()
        ];
        $this->redis->hMSet('task:' . $taskId, $statusInfo);

        return true;
    }

    /**
     * Get all tasks from queue
     *
     * @param string $queue
     *
     * @return array
     * @throws RedisException
     */
    public function getDataByQueueName(string $queue): array
    {
        return $this->redis->lRange($queue, 0, -1);
    }

    /**
     * Get data from queue by index
     *
     * @param string $queue
     * @param int $index
     *
     * @return string|bool
     * @throws RedisException
     */
    public function getTaskByIndex(string $queue, int $index): string|bool
    {
        return $this->redis->lIndex($queue, $index);
    }

    /**
     * @param string $taskId
     *
     * @return string|true
     * @throws RedisException
     */
    public function getStatusByTaskId(string $taskId): string|bool
    {
        $status = $this->redis->hGet('task:' . $taskId, 'status');
        if (!$status){
            return 'Not found the task id: ' . $taskId;
        }
       return true;
    }

    /**
     * Getting and processing a task from a queue
     *
     * @param string $queue
     *
     * @return bool
     * @throws RedisException
     */
    public function runQueue(string $queue): bool
    {
        // Getting and processing a task from a queue
        $response =  $this->redis->rPop($queue);
        $taskId = json_decode($response, true)['task_id'];

        // update task status
        $this->redis->hSet('task:' . $taskId, 'status', 'processing');
        $this->redis->hSet('task:' . $taskId, 'timestamp', time());

        return true;
    }

    /**
     * @param string $taskId
     * @param string|null $status
     * @param array $params
     *
     * @return bool|string
     * @throws RedisException
     */
    public function setStatus(string $taskId, string $status = null, array $params = []): bool|string
    {
        $task =  $this->redis->hGet('task:' . $taskId, 'status');
        if (!$task){
            return 'Not found the task id: ' . $taskId;
        }

        if ($task === 'in process') {
            return 'At the beginning, start the queue';
        }

        $status = !is_null($status) ? $status : 'completed';
        $this->redis->hSet('task:' . $taskId, 'status', $status);
        $this->redis->hSet('task:' . $taskId, 'timestamp', time());

        //Saving the task in a separate cache in case of failure
        if($status === 'failed') {
            $this->redis->hSet('failed_tasks', $taskId, json_encode($params));
        }

        return true;
    }

    /**
     * @param string $desiredStatus
     *
     * @return array
     * @throws RedisException
     */
    public function getTasksByStatus(string $desiredStatus): array
    {
        $keys = $this->redis->keys('task:*');
        $tasks = [];
        foreach ($keys as $key) {
            $status = $this->redis->hGet($key, 'status');
            if ($status == $desiredStatus) {
                $tasks[] = $this->redis->hGetAll($key);
            }
        }

        return $tasks;
    }

    /**
     * @return array
     * @throws RedisException
     */
    public function getFailedTasks(): array
    {
        return $this->redis->hGetAll('failed_tasks');
    }

    /**
     * @param string $taskId
     *
     * @return void
     * @throws RedisException
     */
    public function deleteTask(string $taskId): void
    {
        $this->redis->hDel("failed_tasks", $taskId);
    }

    /**
     * @return Settings
     */
    private function _getSettings(): Settings
    {
        /** @var array $settings Settings array from settingsSelector.php */
        require __DIR__ . '/../../../../settings/settingsSelector.php';

        return new Settings($settings);
    }
}