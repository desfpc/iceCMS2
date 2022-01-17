<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Redis Caching Class
 */

namespace iceCMS2\Caching;

use redka\redka;

class Redis implements CachingInterface
{
    public bool $connected = false;
    public $host;
    public $port;
    public $errors = [];
    private redka $redis;

    /**
     * Cacher constructor.
     *
     * @param string $host
     * @param int $port
     */
    public function __construct($host = 'localhost', $port = 6379)
    {
        $this->host = $host;
        $this->port = $port;

        $this->redis = new redka($this->host, $this->port);
        $this->redis->connect();

        if ($this->redis->connect()) {
            if ($this->redis->status == 1) {
                $this->connected = true;
                return true;
            }
        }

        $this->errors[] = 'Failed to connect to Redis';
        return false;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        if ($this->connected) {
            return ($this->redis->has($key));
        }
        throw new \Exception('No Redis connection');
    }

    /**
     * @inheritDoc
     */
    public function findKeys(string $pattern): mixed
    {
        if ($this->connected) {
            return ($this->redis->findKeys($pattern));
        }
        throw new \Exception('No Redis connection');
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, bool $decode = false): mixed
    {
        if ($this->connected) {
            $this->key = $key;
            $this->value = $this->redis->get($key);

            if ($decode) {
                return (json_decode($this->value, true));
            }
            return ($this->value);
        }
        throw new \Exception('No Redis connection');
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, ?int $expired = null): bool
    {
        if ($this->connected) {
            $this->key = $key;
            $this->value = $value;
            $this->expired = $expired;

            return $this->redis->set($this->key, $this->value, $this->expired);
        }
        throw new \Exception('No Redis connection');
    }

    /**
     * @inheritDoc
     */
    public function del(string $key): bool
    {
        if ($this->connected) {
            $this->key = $key;
            return $this->redis->del($this->key);
        }
        throw new \Exception('No Redis connection');
    }
}