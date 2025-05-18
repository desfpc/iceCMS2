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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->CharSet = $mail::CHARSET_UTF8;
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host = $this->_settings->email->smtp;
            $mail->SMTPAuth = true;
            $mail->Username = $this->_settings->email->mail;
            $mail->Password = $this->_settings->email->pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->_settings->email->port;

            $mail->setFrom($this->_settings->email->mail, 'Nozhove.com Mailer');
            $mail->addAddress($this->_to, $this->_toName);

            if (is_array($attachments) && count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    $mail->addAttachment($attachment);
                }
            }

            $mail->isHTML();
            $mail->Subject = $this->_theme;
            $mail->Body = $this->_text;

            $mail->send();

            return true;
        } catch (Exception $e) {

            (LoggerFactory::instance($this->_settings))::log(
                $this->_settings,
                'email',
                [
                    'message' => $e->getMessage(),
                    'type' => 'email',
                    'to' => $this->_to,
                    'trace' => $e->getTraceAsString(),
                ]
            );

            return false;
        }
    }
}