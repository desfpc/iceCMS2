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
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;
use iceCMS2\Types\UnixTime;

class User extends AbstractEntity
{
    /** @var string User status created */
    public const STATUS_CREATED = 'created';
    /** @var string User status active */
    public const STATUS_ACTIVE = 'active';
    /** @var string User status deleted */
    public const STATUS_DELETED = 'deleted';

    /** @var string User role user */
    public const ROLE_USER = 'user';
    /** @var string User role moderator */
    public const ROLE_MODERATOR = 'moderator';
    /** @var string User role admin */
    public const ROLE_ADMIN = 'admin';

    /** @var string User sex male */
    public const SEX_MALE = 'male';
    /** @var string User sex female */
    public const SEX_FEMALE = 'female';
    /** @var string User sex other */
    public const SEX_OTHER = 'other';

    /** @var int Avatar image size in px */
    private const AVATAR_SIZE = 200;

    /** @var string Avatar file name prefix */
    public const AVATAR_FILE_NAME = 'avatar';

    /** @var string Entity DB table name */
    protected string $_dbtable = 'users';

    /** @var array Array of needed to approve values */
    protected array $_needToApproveValues = ['phone', 'email'];

    /** @var FileImage|null User avatar obj */
    public ?FileImage $avatar = null;

    /** @var string|null User avatar file url */
    public ?string $avatarUrl = null;

    /** @var array|null Validators for values by key */
    protected ?array $_validators = [
        'password' => 'password',
        'email' => ['email', 'uniqueString'],
        'phone' => 'phone|empty',
        'telegram' => 'telegram|empty',
        'language' => 'language',
        'name' => 'string|empty',
        'nikname' => 'uniqueString',
        'status' => 'enum',
        'role' => 'enum',
        'rating' => 'float',
        'avatar' => 'int|empty',
        'email_approve_code' => 'string|empty',
        'email_approved' => 'int',
        'email_send_time' => 'unixtime|empty',
        'phone_approve_code' => 'string|empty',
        'phone_approved' => 'int',
        'phone_send_time' => 'unixtime|empty',
        'created_time' => 'unixtime|empty',
        'sex' => 'enum',
        'contacts' => 'json|empty',
    ];

    /** @var array|null Modificators for values by key */
    protected ?array $_modificators = [
        'password' => 'password',
        'created_time' => 'nowUnixtime',
    ];

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
            $this->avatar = new FileImage($this->_settings, (int)$this->get('avatar'));
            if (!$this->avatar->load()){
                $this->avatar = null;
                $this->errors[] = LocaleText::get($this->_settings, 'user/errors/User avatar load error');
            } else {
                $this->avatarUrl = self::getAvatarUrl($this->_settings, $this->avatar);
                if (is_null($this->avatarUrl)) {
                    $this->errors[] = LocaleText::get($this->_settings, 'user/errors/User avatar save error');
                }
            }
        }
    }

    /**
     * Get Avatar URL by image ID
     *
     * @param Settings $settings
     * @param int|FileImage|null $avatarId
     * @return string|null
     * @throws Exception
     */
    public static function getAvatarUrl(Settings $settings, int|FileImage|null $avatarId = null): ?string
    {
        if (!is_null($avatarId)) {

            if (is_int($avatarId)) {
                $avatar = new FileImage($settings, $avatarId);
                if (!$avatar->load()) {
                    return null;
                }
            } else {
                $avatar = $avatarId;
                if (!$avatar->isLoaded && !$avatar->load()) {
                    return null;
                }
            }
            $fileSizes = $avatar->getImageSizes();
            $avatarSizeId = null;
            if (!empty($fileSizes)) {
                foreach ($fileSizes as $size) {
                    if ($size['string_id'] == self::AVATAR_FILE_NAME) {
                        $avatarSizeId = $size['id'];
                        break;
                    }
                }
            }
            if (is_null($avatarSizeId)) {
                $fileSize = new ImageSize($settings);
                if (!$fileSize->loadByParam('string_id', self::AVATAR_FILE_NAME)) {
                    $fileSize->set([
                        'width' => self::AVATAR_SIZE,
                        'height' => self::AVATAR_SIZE,
                        'is_crop' => 1,
                        'string_id' => self::AVATAR_FILE_NAME,
                    ]);
                    if (!$fileSize->save()) {
                        return null;
                    }
                }
                if ($fileSize->isLoaded) {
                    $avatarSizeId = (int)$fileSize->get('id');
                    $avatar->addImageSize($avatarSizeId);
                    $avatar->buildImageSizeFiles();
                }
            }
            return $avatar->getUrl($avatarSizeId);
        }

        return null;
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

    /**
     * Check password
     *
     * @param string $password
     * @return bool
     * @throws Exception
     */
    public function checkPassword(string $password): bool
    {
        if (!$this->isLoaded) {
            throw new Exception('User is not loaded');
        }
        return password_verify($password, $this->get('password'));
    }
}