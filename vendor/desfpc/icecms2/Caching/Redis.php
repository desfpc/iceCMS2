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

use iceCMS2\Tools\Exception;
use Redis as Rediska;
use Throwable;

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

    /** @var ?Rediska $redis Redis client object */
    private ?Rediska $redis = null;

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
            $this->redis = new Rediska();
            if (!$this->redis->connect($this->host, $this->port)) {
                $this->errors[] = 'Redis connection error';
            }
        } catch (Throwable $e) {
            $this->errors[] = $e->getMessage();
        }

        if (!empty($this->errors)) {
            throw new Exception('Error: ' . implode(', ', $this->errors));
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function has(string $key): bool
    {
        try {
            return (bool)($this->redis->exists($key));
        } catch (\Exception $e) {
            throw new Exception('Redis error: ' . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function findKeys(string $pattern): array|Rediska
    {
        try {
            return ($this->redis->keys($pattern));
        } catch (\Exception $e) {
            throw new Exception('Redis error: ' . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function get(string $key, bool $decode = false): mixed
    {
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

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function set(string $key, mixed $value, ?int $expired = null): bool
    {
        try {
            $res = $this->redis->set($key, $value, $expired);
        } catch (\Exception $e) {
            throw new Exception('Redis error: ' . $e->getMessage());
        }

        return $res;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function del(string $key): bool
    {
        $res = $this->redis->del($key);
        if ($res === 1) {
            return true;
        }
        return false;
    }
}