<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * API v1 User Controller Class
 */

namespace app\Controllers\vendor\api\v1;

use iceCMS2\Controller\AbstractTokenAuthorizationController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\FileImage;
use iceCMS2\Services\UserService;
use iceCMS2\Tools\Exception;
use iceCMS2\Models\User as UserModel;

class User extends AbstractTokenAuthorizationController implements ControllerInterface
{
    public string $title = 'User';

    public function auth(): void
    {
        try {
            $jsonData = json_decode(file_get_contents('php://input'), true);

            //die(file_get_contents('php://input'));

            if ($tokens = $this->authorization->getTokens($jsonData['email'], $jsonData['password'])) {
                $this->renderJson($tokens, true);
            } else {
                $this->renderJson(['message' => 'Wrong email or password'], false);
            }
        } catch(Exception $e) {
            $this->renderJson(['message' => 'Wrong auth request'], false);
            return;
        }
    }

    /**
     * Return list of users (full entities) by IDs //TODO caching
     *
     * @return void
     */
    public function list(): void
    {
        $this->requestParameters->getRequestValues(['users']);
        if (empty($this->requestParameters->values->users)) {
            $this->renderJson(['message' => 'Empty users ids string'], false);
            return;
        }

        $list = explode(',', $this->requestParameters->values->users);

        try {
            $list = array_map(function ($userId) {
                $user = new UserModel($this->settings);
                if (!$user->load((int)$userId)) {
                    throw new Exception('Wrong User ID');
                }
                $userValues = $user->get();
                unset($userValues['password']);
                return $userValues;
            }, $list);
        } catch (Exception $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
            return;
        }

        $this->renderJson($list, true);
    }

    /**
     * Return User by ID
     *
     * @return void
     * @throws Exception
     */
    public function get(): void
    {
        if (empty($this->routing->pathInfo['query_vars']['id'])) {
            if ($this->authorization->getAuthStatus() === true) {
                $userId = (int) $this->authorization->getUser()->get('id');
            } else {
                $this->renderJson(['message' => 'No User ID', 'authorization' => $this->authorization->getAuthStatus()], false);
                return;
            }
        } else {
            $userId = (int) $this->routing->pathInfo['query_vars']['id'];
        }

        try {
            $user = new UserModel($this->settings);
        } catch (Exception $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
            return;
        }

        if (!$user->load($userId)) {
            $this->renderJson(['message' => 'Wrong User ID'], false);
            return;
        }

        $out = $user->get();
        $user->loadAvatar();
        $out['avatarUrl'] = $user->avatarUrl;

        if ($this->authorization->getAuthStatus() === true && (int) $this->authorization->getUser()->get('id') === $userId) {
            unset($out['password']);
            $out['contacts'] = json_decode($out['contacts'], true);
        } else {
            unset(
                $out['password'],
                $out['email'],
                $out['phone'],
                $out['telegram'],
                $out['name'],
                $out['email_approve_code'],
                $out['email_approved'],
                $out['email_send_time'],
                $out['phone_approve_code'],
                $out['phone_approved'],
                $out['phone_send_time'],
                $out['contacts'],
            );
        }

        $this->renderJson($out, true);
    }

    /**
     * Upload user avatar
     *
     * @return void
     * @throws Exception
     */
    public function uploadAvatar(): void
    {
        $this->_authorizationCheck();

        /** @var UserModel $user */
        $user = $this->authorization->getUser();

        $file = new FileImage($this->settings);

        if ($file->savePostFile('file')) {
            if (!is_null($user->get('avatar'))) {
                $oldAvatar = new FileImage($this->settings);
                $oldAvatar->load((int)$user->get('avatar'));
                $oldAvatar->del(null, true);
            }

            $fileId = $file->get('id');

            $user->set('avatar', $fileId);
            $user->save();
            $user->loadAvatar();
            $this->renderJson(['file' => $fileId, 'url' => $user->avatarUrl], true);
        } else {
            $this->renderJson(['message' => 'Error in avatar uploading'], false);
        }
    }

    /**
     * Update user password
     *
     * @return void
     * @throws Exception
     */
    public function changePassword(): void
    {
        $this->_authorizationCheck();

        /** @var UserModel $user */
        $user = $this->authorization->getUser();

        $data = json_decode(file_get_contents('php://input'), true);

        if ($data['old'] === $data['new']) {
            $this->renderJson(['message' => 'Old and new passwords are equal'], false);
            return;
        }

        if (!$user->checkPassword($data['old'])) {
            $this->renderJson(['message' => 'Wrong old password'], false);
            return;
        }

        $setPassword = UserService::setPassword($user, $data['new'], $data['new']);

        if ($setPassword['status'] === 'success') {
            $this->renderJson(['message' => 'Password updated'], true);
        } else {
            $this->renderJson(['message' => $setPassword['message']], false);
        }
    }

    /**
     * Update user profile
     *
     * @return void
     * @throws Exception
     */
    public function updateProfile(): void
    {
        $this->_authorizationCheck();

        /** @var UserModel $user */
        $user = $this->authorization->getUser();
        $data = json_decode(file_get_contents('php://input'), true);
        $unsetArr = ['avatar', 'email_approve_code', 'email_approved', 'email_send_time', 'phone_approve_code',
            'password', 'phone_approved', 'phone_send_time', 'created_at', 'approved_icon'];
        if (mb_strpos($user->get('email'), 'fake.nozhove.com', 0, 'UTF-8') === false) {
            $unsetArr[] = 'email';
        }

        foreach ($unsetArr as $unset) {
            if (isset($data[$unset])) {
                unset($data[$unset]);
            }
        }

        if (isset($data['contacts'])) {
            try {
                $data['contacts'] = json_encode($data['contacts']);
            } catch (\Throwable $e) {
                $data['contacts'] = null;
            }
        }

        if (isset($data['languages'])) {
            try {
                $data['languages'] = json_encode($data['languages']);
            } catch (\Throwable $e) {
                $data['languages'] = '[]';
            }
        }

        try {
            $user->set($data);
            if ($user->save()) {
                $this->renderJson(['message' => 'Profile updated'], true);
            } else {
                $this->renderJson(['message' => 'Error in profile updating', 'errors' => $user->errors], false);
            }
        } catch (Exception $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
            return;
        }
    }
}