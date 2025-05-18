<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * DateTime type class
 */

namespace iceCMS2\Types;

class UnixTime
{
    /** @var int|null Timestamp int value */
    private ?int $_timestamp = null;

    /**
     * Constructor method
     *
     * @param int|string|null $time
     */
    public function __construct(int|string|null $time = null)
    {
        $this->set($time);
    }

    /**
     * How much seconds has passed since the timestamp
     *
     * @return int
     */
    public function passedSeconds(): int
    {
        return time() - $this->_timestamp;
    }

    public function passedString(int $seconds, int $timeShift = 0, string $format = 'd.m.Y'): array
    {
        $seconds = time() - $this->_timestamp;
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);

        if ($hours > 24) {
            return [date($format, ($this->_timestamp + $timeShift)), null];
        } elseif($minutes > 60) {
            return ['hours ago {time}', $hours];
        } elseif($seconds > 60) {
            return ['minutes ago {time}', $minutes];
        } else {
            return ['seconds ago {time}', $seconds];
        }
    }

    /**
     * Get int timestamp
     *
     * @return false|int|null
     */
    public function get(): bool|int|null
    {
        return $this->_timestamp;
    }

    /**
     * Setting timestamp
     *
     * @param int|string|null $time
     * @return void
     */
    public function set(int|string|null $time = null): void
    {
        if (is_null($time)) {
            $time = time();
        } elseif (is_string($time)) {
            $time = strtotime($time);
        }

        $this->_timestamp = $time;
    }

    /**
     * Magick to string conversion
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->get();
    }
}