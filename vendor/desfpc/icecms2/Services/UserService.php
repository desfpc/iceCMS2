<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User service Class
 */

namespace iceCMS2\Services;

use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;

class UserService
{
    public static function setPassword(User $user, string $password, string $repeatPassword): array
    {
        //check empty password
        if (empty($password)) {
            return ['status' => 'error', 'message' => 'Password is empty'];
        }

        //check password equals
        if ($password !== $repeatPassword) {
            return ['status' => 'error', 'message' => 'Passwords not equals'];
        }

        //check password
        if (strlen($password) < 8) {
            return ['status' => 'error', 'message' => 'Password length must be more than 6 characters'];
        }

        //check for numeric and alphabetic characters
        if (!preg_match('/[0-9]/', $password) || !preg_match('/[a-zA-Z]/', $password)) {
            return ['status' => 'error', 'message' => 'Password must contain at least one letter and one number'];
        }

        //set password
        try {
            $user->set('password', $password);
            if ($user->save()) {
                return ['status' => 'success', 'message' => 'Password updated'];
            } else {
                return ['status' => 'error', 'message' => 'Password not updated'];
            }
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}