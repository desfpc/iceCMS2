<?php

namespace iceCMS2\Queue;

use iceCMS2\Settings\Settings;

class QueueFactory
{
    /**
     * @param string|null $type
     *
     * @return RedisQueue|DBQueue
     */
    public static function queue(?string $type = null): RedisQueue|DBQueue
    {
        /** @var array $settings Settings array from settingsSelector.php */
        require __DIR__ . '/../../../../settings/settingsSelector.php';

        $settings = new Settings($settings);

        return self::instance($settings, $type);
    }

    /**
     * Get a queue instance
     *
     * @param Settings $settings
     * @param string|null $type
     *
     * @return DBQueue|RedisQueue
     */
    private static function instance(Settings $settings, ?string $type): DBQueue|RedisQueue
    {
        if (is_null($type)) {
            $type = $settings->queue->default;
        }
        return match ($type) {
            'mysql' => new DBQueue(),
            default => new RedisQueue()
        };
    }
}