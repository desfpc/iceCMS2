<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * TODO Email Message class
 */

namespace iceCMS2\Messages;

use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class Email implements MessageInterface
{
    public function __construct()
    {

    }

    public function setFrom(string $from, ?string $name = null): MessageInterface
    {
        // TODO: Implement setFrom() method.
    }

    public function setTo(string $to, ?string $name = null): MessageInterface
    {
        // TODO: Implement setTo() method.
    }

    public function setTheme(string $theme): MessageInterface
    {
        // TODO: Implement setTheme() method.
    }

    public function setText(string $text): MessageInterface
    {
        // TODO: Implement setText() method.
    }

    public function send(): bool
    {
        // TODO: Implement send() method.
    }
}