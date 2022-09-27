<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User entity class
 */

namespace iceCMS2\Models;

use iceCMS2\Helpers\Strings;
use iceCMS2\Locale\LocaleText;
use iceCMS2\Messages\MessageFactory;
use iceCMS2\Tools\Exception;
use iceCMS2\Types\UnixTime;

class User extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'users';

    /** @var array Array of needed to approve values */
    protected array $_needToApproveValues = ['phone', 'email'];

    /** @var FileImage|null User avatar obj */
    public ?FileImage $avatar = null;

    /**
     * Logic after Entity load() method
     *
     * @return void
     * @throws Exception
     */
    protected function _afterLoad(): void
    {
        //avatar load
        if ($this->get('avatar') > 0) {
            $this->avatar = new FileImage($this->_settings, $this->get('avatar'));
            if (!$this->avatar->load()){
                $this->avatar = null;
                $this->errors[] = LocaleText::get($this->_settings, 'user/errors/User avatar load error');
            }
        }
    }

    /**
     * Check approved code and update approved status
     *
     * @param string $codeType
     * @param string $inputValue
     * @return bool
     * @throws Exception
     */
    public function checkApproveCode(string $codeType, string $inputValue): bool
    {
        if ($this->get($codeType . '_approve_code') === $inputValue) {
            $this->set($codeType. '_approved', 1);
            return $this->save();
        }

        return false;
    }

    /**
     * Send approve code to user
     *
     * @param string $codeType
     * @return bool
     * @throws Exception
     */
    public function sendApproveCode(string $codeType): bool
    {
        $code = $this->_getApproveCode();
        $this->set($codeType. '_approve_code', $code);
        $this->set($codeType. '_approved', 0);
        $this->set($codeType. '_send_time', new UnixTime());
        if ($this->save() && $this->_sendApproveCodeMessage($codeType)) {
            return true;
        }

        return false;
    }

    /**
     * Send approve code message
     *
     * @param string $codeType
     * @return bool
     * @throws Exception
     */
    private function _sendApproveCodeMessage(string $codeType): bool
    {
        $code = $this->get($codeType. '_approve_code');

        $message = MessageFactory::instance($this->_settings, $codeType)
            ->setTo($this->get($codeType), $this->get('name'))
            ->setTheme(LocaleText::get(
                $this->_settings,
                'user/approve/Code for approve {codeType}',
                ['codeType' => LocaleText::get($this->_settings, 'user/approve/genitiveСase/' . $codeType)]
            ));

        switch ($codeType) {
            case 'phone':
                $message->setFrom($this->_settings->site->name, $this->_settings->site->name)
                    ->setText('Code: ' . $code);
                break;
            case 'email':
                $message->setFrom($this->_settings->email->mail, $this->_settings->email->signature)
                    ->setText(LocaleText::get($this->_settings, 'user/approve/Code') . ' '
                        . $this->get('name') . '!'
                        . '<br><br>' . LocaleText::get(
                            $this->_settings,
                            'Your approval code on {siteName}: <b>{code}</b>', [
                            'siteName' => $this->_settings->site->title,
                            'code' => $code
                        ])
                        . '<br><br>' . LocaleText::get(
                            $this->_settings,
                            '{team} team',
                            ['team' => $this->_settings->site->title]
                        )
                    );
                break;
        }

        return $message->send();
    }

    /**
     * Generate and get approve code string
     *
     * @return string
     */
    private function _getApproveCode():string
    {
        return Strings::getRandomString(6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
    }
}