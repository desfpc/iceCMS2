<?php

namespace iceCMS2\Queue;

interface QueueInterface
{
    /**
     * @param string $queue
     * @param array $params
     *
     * @return bool
     */
    public function create(string $queue, array $params): bool;

    /**
     * @param string $queue
     *
     * @return array
     */
    public function getDataByQueueName(string $queue): array;

    /**
     * @param string $queue
     * @param int $index
     *
     * @return mixed
     */
    public function getTaskByIndex(string $queue, int $index): mixed;

    /**
     * @param string $taskId
     *
     * @return mixed
     */
    public function getStatusByTaskId(string $taskId): mixed;

    /**
     * @param string $queue
     *
     * @return bool
     */
    public function runQueue(string $queue): bool;

    /**
     * @param string $taskId
     * @param string|null $status
     * @param array $params
     *
     * @return mixed
     */
    public function setStatus(string $taskId, string $status = null, array $params = []): mixed;

    /**
     * @param string $desiredStatus
     *
     * @return array
     */
    public function getTasksByStatus(string $desiredStatus): array;
}