<?php

namespace iceCMS2\Queue;

use iceCMS2\DB\DBFactory;
use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class DBQueue implements QueueInterface
{
    /** @var string */
    private $_dbtable = 'queues';

    /**
     * @param string $queue
     * @param array $params
     *
     * @return bool
     * @throws Exception
     */
    public function create(string $queue, array $params): bool
    {
        $taskId = uniqid();
        $db = $this->_getDDFactory();
        $params = json_encode($params);

        $keys = 'queue,task_id,value,status';
        $values = "'$queue','$taskId','$params','in process'";

        $sql = 'INSERT INTO `' . $this->_dbtable . '` (' . $keys . ') VALUES (' . $values . ')';
        $db->query($sql);

        return true;
    }

    /**
     * Get all tasks from queue
     *
     * @param string $queue
     *
     * @return array
     * @throws Exception
     */
    public function getDataByQueueName(string $queue): array
    {
        $db = $this->_getDDFactory();
        $sql = 'SELECT * FROM `' . $this->_dbtable . '` WHERE queue = "' . $queue . '"';
        return $db->query($sql);
    }

    /**
     * Get data from queue by index
     *
     * @param string $queue
     * @param int $index
     *
     * @return array
     * @throws Exception
     */
    public function getTaskByIndex(string $queue, int $index): array
    {
        $db = $this->_getDDFactory();
        $sql = 'SELECT * FROM `' . $this->_dbtable . '` WHERE id = ' . $index . ' AND queue = "' . $queue . '"';
        return $db->query($sql);
    }

    /**
     * @param string $taskId
     *
     * @return array|null
     * @throws Exception
     */
    public function getStatusByTaskId(string $taskId): ?array
    {
        $db = $this->_getDDFactory();
        $sql = 'SELECT * FROM `' . $this->_dbtable . '` WHERE task_id = "' . $taskId . '"';
        return $db->query($sql);
    }

    /**
     * Getting and processing a task from a queue
     *
     * @param string $queue
     *
     * @return bool
     * @throws Exception
     */
    public function runQueue(string $queue): bool
    {
        $db = $this->_getDDFactory();
        $sql = 'SELECT * FROM `' . $this->_dbtable . '` WHERE queue = "' . $queue . '" ORDER BY created_time DESC LIMIT 1';
        $task = $db->query($sql);

        $sql = 'UPDATE `' . $this->_dbtable . '` SET status = "processing" WHERE queue = "' . $queue . '" ORDER BY created_time DESC LIMIT 1';
        $db->query($sql);

        return true;
    }

    /**
     * @param string $taskId
     * @param string|null $status
     * @param array $params
     *
     * @return array|string
     * @throws Exception
     */
    public function setStatus(string $taskId, string $status = null, array $params = []): array|string
    {
        $db = $this->_getDDFactory();
        $sql = 'SELECT * FROM `' . $this->_dbtable . '` WHERE task_id = "' . $taskId . '"';
        $task = $db->query($sql);

        if (empty($task)) {
            return 'Not found the task id: ' . $taskId;
        }
        if ($task[0]['status'] === 'in process') {
            return 'At the beginning, start the queue';
        }

        //Saving the task in a separate cache in case of failure
        if ($status === 'failed') {
            $sql = 'UPDATE `' . $this->_dbtable . '` SET status = "failed" WHERE task_id = "' . $taskId . '"';
            $task = $db->query($sql);
            return $task;
        }

        $sql = 'UPDATE `' . $this->_dbtable . '` SET status = "completed" WHERE task_id = "' . $taskId . '"';
        $db->query($sql);

        return 'success';
    }

    /**
     * @param string $desiredStatus
     *
     * @return array
     * @throws Exception
     */
    public function getTasksByStatus(string $desiredStatus): array
    {
        $db = $this->_getDDFactory();
        $sql = 'SELECT * FROM `' . $this->_dbtable . '` WHERE status = "' . $desiredStatus . '"';
        $task = $db->query($sql);
        return $task;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getFailedTasks(): array
    {
        $desiredStatus = 'failde';
        $db = $this->_getDDFactory();
        $sql = 'SELECT * FROM `' . $this->_dbtable . '` WHERE status = "' . $desiredStatus . '"';
        $task = $db->query($sql);
        return $task;
    }

    /**
     * @param string $taskId
     *
     * @return string
     * @throws Exception
     */
    public function deleteTask(string $taskId): string
    {
        $db = $this->_getDDFactory();
        $sql = 'SELECT * FROM `' . $this->_dbtable . '` WHERE task_id = "' . $taskId . '"';
        $task = $db->query($sql);

        if (empty($task)) {
            return 'Not found the task id: ' . $taskId;
        }
        if ($task[0]['status'] === 'in process') {
            return 'At the beginning, start the queue';
        }

        $sql = 'UPDATE `' . $this->_dbtable . '` SET status = "completed" WHERE task_id = "' . $taskId . '"';
        $db->query($sql);

        return 'success';
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

    /**
     * @return DBInterface|null
     * @throws Exception
     */
    private function _getDDFactory(): ?DBInterface
    {
        $settings = self::_getSettings();
        $db = (new DBFactory($settings))->db;
        $db->connect();
        return $db;
    }
}