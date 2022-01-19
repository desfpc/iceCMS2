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

class Redis implements CachingInterface
{
    /** @var bool Connected flag */
    public bool $connected = false;

    /** @var string Redis host */
    public $host;

    /** @var int Redis port */
    public $port;

    /** @var array Cacher errors array */
    public $errors = [];

    /** @var Redka Redis client object */
    private Redka $redis;

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

            $res = $this->redis->set($this->key, $this->value, $this->expired);
            if ($res === 'OK') {
                return true;
            }
            return false;
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
            $res = $this->redis->del($this->key);
            if ($res === 'OK' || $res === '1' || $res === 1) {
                return true;
            }
            return false;
        }
        throw new \Exception('No Redis connection');
    }
}