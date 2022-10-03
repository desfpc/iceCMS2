<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Email SMTP Transport class
 */

namespace iceCMS2\Messages;

use DateTime;
use iceCMS2\Logger\LoggerFactory;
use pechkin\pechkin;

class EmailSMTPTransport extends AbstractMessage
{
    /**
     * Form email header: "name <email>"
     *
     * @param string $email
     * @param string|null $name
     * @return string
     */
    public static function makeEmailFullName(string $email, ?string $name = null): string
    {
        if(is_null($name) || $name === '') return $email;
        return $name.' <'.$email.'>';
    }

    /**
     * Send message
     *
     * @param array|null $attachments
     * @return bool
     */
    public function send(?array $attachments = null): bool
    {
        $this->_sendDate = new DateTime();

        $mail = new pechkin(
            $this->_settings->email->smtp,
            $this->_settings->email->port,
            $this->_settings->email->mail,
            $this->_settings->email->pass,
            'ssl',
            60,
            false
        );

        if (is_array($attachments) && count($attachments) > 0) {
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment);
            }
        }

        if ($mail->send(
            self::makeEmailFullName($this->_to, $this->_toName),
            self::makeEmailFullName($this->_from, $this->_fromName),
            $this->_theme,
            $this->_text)
        ) {
            (LoggerFactory::instance($this->_settings))::log(
                $this->_settings,
                'email',
                [
                    'type' => 'email',
                    'to' => $this->_to,
                    'to_name' => $this->_toName,
                    'from' => $this->_from,
                    'from_name' => $this->_fromName,
                    'theme' => $this->_theme,
                    'text' => $this->_text,
                    'date' => $this->_sendDate->format('Y-m-d H:i:s')
                ]
            );

            return true;
        }

    return false;
    }
}