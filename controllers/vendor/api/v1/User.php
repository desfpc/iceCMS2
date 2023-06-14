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

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\FileImage;
use iceCMS2\Tools\Exception;
use iceCMS2\Models\User as UserModel;

class User extends AbstractController implements ControllerInterface
{
    public string $title = 'User';

    /**
     * Return list of users JSON TODO caching
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
        if (!isset($this->routing->pathInfo['query_vars']['id'])) {
            $this->renderJson(['message' => 'No User ID passed'], false);
            return;
        }

        $userId = $this->routing->pathInfo['query_vars']['id'];

        try {
            $user = new UserModel($this->settings);
        } catch (Exception $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
            return;
        }

        if (!$user->load((int)$userId)) {
            $this->renderJson(['message' => 'Wrong User ID'], false);
        }

        $out = $user->get();
        unset($out['password']);

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
                $oldAvatar->del();
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
}