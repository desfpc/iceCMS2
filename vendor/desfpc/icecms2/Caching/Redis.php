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

use desfpc\Redka\Redka;
use iceCMS2\Tools\Exception;

class Redis implements CachingInterface
{
    /** @var bool Connected flag */
    public bool $connected = false;

    /** @var string Redis host */
    public string $host;

    /** @var int Redis port */
    public int $port;

    /** @var array Cacher errors array */
    public array $errors = [];

    /** @var Redka Redis client object */
    private Redka $redis;

    /**
     * Cacher constructor.
     *
     * @param string $host
     * @param int $port
     */
    public function __construct(string $host = 'localhost', int $port = 6379)
    {
        $this->host = $host;
        $this->port = $port;

        try {
            $this->redis = new Redka($this->host, $this->port);
            $this->redis->connect();
            if ($this->redis->status == 1) {
                $this->connected = true;
            }
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function has(string $key): bool
    {
        if ($this->connected) {
            try {
                return ($this->redis->has($key));
            } catch (\Exception $e) {
                throw new Exception('Redis error: ' . $e->getMessage());
            }
        }
        throw new Exception('No Redis connection');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function findKeys(string $pattern): mixed
    {
        if ($this->connected) {
            try {
                return ($this->redis->findKeys($pattern));
            } catch (\Exception $e) {
                throw new Exception('Redis error: ' . $e->getMessage());
            }
        }
        throw new Exception('No Redis connection');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function get(string $key, bool $decode = false): mixed
    {
        if ($this->connected) {
            try {
                $value = $this->redis->get($key);
            } catch (\Exception $e) {
                throw new Exception('Redis error: ' . $e->getMessage());
            }

            if ($decode) {
                return (json_decode($value, true));
            }
            return ($value);
        }
        throw new Exception('No Redis connection');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function set(string $key, mixed $value, ?int $expired = null): bool
    {
        if ($this->connected) {
            try {
                $res = $this->redis->set($key, $value, $expired);
            } catch (\Exception $e) {
                throw new Exception('Redis error: ' . $e->getMessage());
            }
            if ($res === 'OK') {
                return true;
            }
            return false;
        }
        throw new Exception('No Redis connection');
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function del(string $key): bool
    {
        if ($this->connected) {
            $res = $this->redis->del($key);
            if ($res === 'OK' || $res === '1' || $res === 1) {
                return true;
            }
            return false;
        }
        throw new Exception('No Redis connection');
    }
}