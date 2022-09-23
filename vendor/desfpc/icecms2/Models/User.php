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
use iceCMS2\Messages\MessageFactory;
use iceCMS2\Tools\Exception;

class User extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'users';

    /** @var array Array of needed to approve values */
    protected array $_needToApproveValues = ['phone', 'email'];

    /**
     * TODO Logics after Entity load() method
     *
     * @return void
     */
    protected function _afterLoad(): void
    {

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
        $this->set($codeType. '_send_time');
        if ($this->save() && $this->_sendApproveCodeMessage($codeType)) {
            return true;
        }

        return false;
    }

    /**
     * TODO send approve code message
     *
     * @param string $codeType
     * @return bool
     * @throws Exception
     */
    private function _sendApproveCodeMessage(string $codeType): bool
    {
        $code = $this->_getApproveCode();
        $message = MessageFactory::get($this->_settings, $codeType)->setTo($this->get($codeType), $this->get('name'));

        switch ($codeType) {
            case 'phone':

                break;
            case 'email':

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