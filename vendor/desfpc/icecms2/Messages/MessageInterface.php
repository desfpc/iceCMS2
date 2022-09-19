<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Message Interface
 */

namespace iceCMS2\Messages;

interface MessageInterface
{
    /**
     * Setting message sender
     *
     * @param string $from
     * @param string|null $name
     * @return MessageInterface
     */
    public function setFrom(string $from, ?string $name = null): self;

    /**
     * Setting message receiver
     *
     * @param string $to
     * @param string|null $name
     * @return MessageInterface
     */
    public function setTo(string $to, ?string $name = null): self;

    /**
     * Setting message theme
     *
     * @param string $theme
     * @return MessageInterface
     */
    public function setTheme(string $theme): self;

    /**
     * Setting message text
     *
     * @param string $text
     * @return MessageInterface
     */
    public function setText(string $text): self;

    /**
     * Sending message
     *
     * @return bool
     */
    public function send(): bool;
}