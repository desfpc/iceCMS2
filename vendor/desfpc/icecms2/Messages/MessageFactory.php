<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Message Factory
 */

namespace iceCMS2\Messages;

use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class MessageFactory
{
    /**
     * Getting Message class
     *
     * @param Settings $settings
     * @param string $receiverType
     * @return MessageInterface
     * @throws Exception
     */
    public static function instance(Settings $settings, string $receiverType): MessageInterface
    {
        return match ($receiverType) {
            'email' => new FakeEmailTransport($settings),
            'phone' => new FakePhoneTransport($settings),
            'push' => new FakePushTransport($settings),
            default => throw new Exception('Wrong message receiver type'),
        };
    }
}