<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Abstract Message class
 */

namespace iceCMS2\Messages;

use DateTime;
use iceCMS2\Settings\Settings;

abstract class AbstractMessage implements MessageInterface
{
    /** @var string|null */
    private ?string $_to = null;

    /** @var string|null */
    private ?string $_toName = null;

    /** @var string|null */
    private ?string $_from = null;

    /** @var string|null */
    private ?string $_fromName = null;

    /** @var string|null */
    private ?string $_theme = null;

    /** @var string|null */
    private ?string $_text = null;

    /** @var DateTime|null */
    private ?DateTime $_sendDate = null;

    /** @var Settings|null */
    private ?Settings $_settings = null;

    /**
     * Class constructor
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->_settings = $settings;
    }

    /**
     * Set Sender and Sender name
     *
     * @param string $from
     * @param string|null $name
     * @return MessageInterface
     */
    public function setFrom(string $from, ?string $name = null): MessageInterface
    {
        $this->_from = $from;
        $this->_fromName = $name;

        return $this;
    }

    /**
     * Set Receiver and Receiver name
     *
     * @param string $to
     * @param string|null $name
     * @return MessageInterface
     */
    public function setTo(string $to, ?string $name = null): MessageInterface
    {
        $this->_to = $to;
        $this->_toName = $name;

        return $this;
    }

    /**
     * Set Message Theme
     *
     * @param string $theme
     * @return MessageInterface
     */
    public function setTheme(string $theme): MessageInterface
    {
        $this->_theme = $theme;

        return $this;
    }

    /**
     * Set Message Text
     *
     * @param string $text
     * @return MessageInterface
     */
    public function setText(string $text): MessageInterface
    {
        $this->_text = $text;

        return $this;
    }

    /**
     * Send message
     *
     * @return bool
     */
    public function send(): bool
    {
        $this->_sendDate = new DateTime();
        return true;
    }

    /**
     * Serializer
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'from' => $this->_from,
            'fromName' => $this->_fromName,
            'to' => $this->_to,
            'toName' => $this->_toName,
            'theme' => $this->_theme,
            'text' => $this->_text,
            'date' => is_null($this->_sendDate) ? null : $this->_sendDate->format('Y-m-d H:i:s'),
        ];
    }
}