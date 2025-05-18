<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * API v1 Admin User Controller Class
 */

namespace app\Controllers\vendor\api\v1;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\DTO\UserListAdminDto;
use iceCMS2\Helpers\Strings;
use iceCMS2\Locale\LocaleText;
use iceCMS2\Models\FileImage;
use iceCMS2\Models\User;
use iceCMS2\Models\UserList;
use iceCMS2\Tools\Exception;

class AdminUser extends AbstractController implements ControllerInterface
{
    private const ROWS_COUNT = 20;
    private const EDITABLE_PROPS = [
        'telegram',
        'role',
        'status',
    ];

    /**
     * Edit User password
     *
     * @return void
     * @throws Exception
     */
    public function password(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $user = $this->_checkUserFromRequest();
        if ($user === null) {
            return;
        }

        $this->requestParameters->getRequestValues(['password', 'repeatPassword']);
        $password = $this->requestParameters->values->password;
        $repeatPassword = $this->requestParameters->values->repeatPassword;

        if ($password !== $repeatPassword) {
            $this->renderJson(['message' => 'Passwords do not match'], false); //TODO add locale
            return;
        }

        $this->renderJson(['message' => 'Password changed - ' .  $this->requestParameters->values->password . ' - ' . $this->requestParameters->values->repeatPassword], true);
    }

    /**
     * Edit User Form
     *
     * @return void
     * @throws Exception
     */
    public function edit(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $user = $this->_checkUserFromRequest();
        if ($user === null) {
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $unsetArr = ['avatar', 'email_approve_code', 'email_approved', 'email_send_time', 'phone_approve_code',
            'password', 'phone_approved', 'phone_send_time', 'created_at',];

        foreach ($unsetArr as $unset) {
            unset($data[$unset]);
        }

        if (isset($data['contacts'])) {
            try {
                $data['contacts'] = json_encode($data['contacts']);
            } catch (\Throwable $e) {
                $data['contacts'] = '[]';
            }
        }

        if (isset($data['languages']) && is_array($data['languages'])) {
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
        }
    }

    /**
     * Upload User Avatar
     *
     * @return void
     * @throws Exception
     */
    public function uploadAvatar(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $user = $this->_checkUserFromRequest();
        if ($user === null) {
            return;
        }

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
            $this->renderJson(['file' => $fileId, 'url' => $user->avatarUrl, 'message' => 'Avatar changed'], true);
        } else {
            $this->renderJson(['message' => 'Error in avatar uploading'], false);
        }
    }

    /**
     * Edit User separate params
     *
     * @return void
     * @throws Exception
     */
    public function editProperty(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $user = $this->_checkUserFromRequest();
        if ($user === null) {
            return;
        }

        $this->requestParameters->getRequestValues(['property', 'value']);

        if (empty($this->requestParameters->values->property) || empty($this->requestParameters->values->value)) {
            $this->renderJson(['message' => 'No property or value passed'], false);
            return;
        }

        if (!in_array($this->requestParameters->values->property, self::EDITABLE_PROPS)) {
            $this->renderJson(['message' => 'Property not editable'], false);
            return;
        }

        $property = $this->requestParameters->values->property;
        $value = $this->requestParameters->values->value;

        try {
            $user->$property = $value;
            $user->save();
        } catch (Exception $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
            return;
        }

        $this->renderJson(['message' => 'User updated'], true);
    }

    /**
     * Delete User by ID
     *
     * @return void
     * @throws Exception
     */
    public function delete(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $user = $this->_checkUserFromRequest();
        if ($user === null) {
            return;
        }

        if (!$user->load($user->id)) {
            $this->renderJson(['message' => 'Wrong User ID'], false);
            return;
        }

        if ($user->del()) {
            $this->renderJson(['message' => 'User deleted'], true);
        } else {
            $this->renderJson(['message' => 'User not deleted'], false);
        }
    }

    /**
     * Get User by ID for editing
     *
     * @return void
     * @throws Exception
     */
    public function get(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $user = $this->_checkUserFromRequest();
        if ($user === null) {
            return;
        }
        $userId = $user->id;

        $user->loadAvatar();
        $userArr = $user->get();
        $userArr['password'] = '';
        if (!empty($userArr['contacts'])) {
            $userArr['contacts'] = json_decode($userArr['contacts'], true);
        }

        $this->renderJson([
            'formData' => $userArr,
            'formTypes' => $this->_getFormTypes(),
            'formNames' => $this->_getFormNames(),
            'formSelects' => $this->_getFormSelects(),
            'formMultiSelects' => $this->_getFormMultiSelects(),
            'formActions' => [
                'save' => '/api/v1/admin/user/' . $userId . '/edit',
                'avatar' => '/api/v1/admin/user/' . $userId . '/avatar',
                'password' => '/api/v1/admin/user/' . $userId . '/password',
            ],
            'formButtons' => [
                'save' => LocaleText::get($this->settings, 'form/actions/save', [], $this->settings->locale),
                'load' => LocaleText::get($this->settings, 'form/actions/reset', [], $this->settings->locale),
                'password' => LocaleText::get($this->settings, 'form/actions/change', [], $this->settings->locale),
            ],
            'formValidators' => $this->_getFormValidators(),
            'formJsons' => [
                'contacts' => [ "Country", "City", "Address", "Zip", "X", "Instagram", "LinkedIn", "YouTube", "Discord", "Website", "Blog", "Other"],
            ],
            'formFiles' => [
                'avatar' => $user->avatarUrl,
            ],
        ], true);
    }

    /**
     * @return ?User
     * @throws Exception
     */
    private function _checkUserFromRequest(): ?User
    {
        if (!isset($this->routing->pathInfo['query_vars']['id'])) {
            $this->renderJson(['message' => 'No User ID passed'], false);
            return null;
        }

        $id = (int)$this->routing->pathInfo['query_vars']['id'];
        $user = new User($this->settings);
        if (!$user->load($id)) {
            $this->renderJson(['message' => 'Wrong User ID'], false);
            return null;
        }

        return $user;
    }

    /**
     * @return string[]
     */
    private function _getFormTypes(): array
    {
        return [
            'id' => 'label',
            'avatar' => 'avatar',
            'contacts' => 'jsonKeyValue',
            'created_time' => 'label',
            'email' => 'label',
            'email_approved' => 'checkbox',
            'email_send_time' => 'label',
            'language' => 'select',
            'languages' => 'multiSelect',
            'password' => 'password',
            'phone_approved' => 'checkbox',
            'phone_send_time' => 'label',
            'role' => 'select',
            'sex' => 'select',
            'status' => 'select',
        ];
    }

    /**
     * Get form validators array
     *
     * @return array
     */
    private function _getFormValidators(): array
    {
        return [
            'avatar' => 'empty|positiveInteger',
            'email_approved' => 'bool',
            'email_approve_code' => 'empty|string',
            'language' => 'string',
            'name' => 'empty|string',
            'nikname' => 'string',
            'phone' => 'empty|string',
            'phone_approved' => 'bool',
            'phone_approve_code' => 'empty|string',
            'rating' => 'float',
            'role' => 'string',
            'sex' => 'string',
            'status' => 'string',
            'telegram' => 'empty|string',
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    private function _getFormNames(): array
    {
        return LocaleText::get($this->settings, 'user/fields', [], $this->settings->locale, true);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function _getFormSelects(): array
    {
        return [
            'language' => array_combine($this->settings->locales, $this->settings->locales),
            'status' => LocaleText::get($this->settings, 'user/statuses', [], $this->settings->locale, true),
            'role' => LocaleText::get($this->settings, 'user/roles', [], $this->settings->locale, true),
            'sex' => LocaleText::get($this->settings, 'user/sexes', [], $this->settings->locale, true),
        ];
    }

    /**
     * @return array
     */
    private function _getFormMultiSelects(): array
    {
        return [
            'languages' => [
                ['text' => 'English', 'value' => 'en'],
                ['text' => 'Русский', 'value' => 'ru'],
                ['text' => 'ქართული', 'value' => 'ge'],
                ['text' => 'Српски', 'value' => 'sr'],
            ],
        ];
    }

    /**
     * Admin User list
     *
     * @return void
     * @throws Exception
     */
    public function list(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $nullDto = new UserListAdminDto();

        // Columns
        $out = [
            'cols' => [
                [
                    'id' => 'id',
                    'name' => LocaleText::get($this->settings, 'user/fields/id', [], $this->settings->locale),
                    'ordered' => true,
                    'action' => 'link',
                    'actionUrl' => '/admin/user/{id}/edit/',
                    'icon' => 'arrow-return-right',
                ],
                [
                    'id' => 'email',
                    'name' => 'Email',
                    'ordered' => true,
                ],
                [
                    'id' => 'telegram',
                    'name' => LocaleText::get($this->settings, 'user/fields/telegram', [], $this->settings->locale),
                    'ordered' => true,
                    'editable' => true,
                    'editUrl' => '/api/v1/admin/user/{id}/edit-prop',
                    'inputType' => 'input',
                ],
                [
                    'id' => 'role',
                    'name' => LocaleText::get($this->settings, 'user/fields/role', [], $this->settings->locale),
                    'ordered' => true,
                    'editable' => true,
                    'editUrl' => '/api/v1/admin/user/{id}/edit-prop',
                    'inputType' => 'select',
                    'selectArray' => [
                        'user' => 'User',
                        'moderator' => 'Moderator',
                        'admin' => 'Admin',
                    ],
                ],
                [
                    'id' => 'status',
                    'name' => LocaleText::get($this->settings, 'user/fields/status', [], $this->settings->locale),
                    'ordered' => true,
                    'editable' => true,
                    'editUrl' => '/api/v1/admin/user/{id}/edit-prop',
                    'inputType' => 'select',
                    'selectArray' => [
                        'created' => 'Created',
                        'active' => 'Active',
                        'deleted' => 'Deleted',
                    ],
                ],
                [
                    'id' => 'created_time',
                    'name' => LocaleText::get($this->settings, 'user/fields/created_time', [], $this->settings->locale),
                    'ordered' => true,
                ],
                [
                    'id' => 'actions',
                    'name' => 'Actions',
                    'ordered' => false,
                    'buttons' => [
                        [
                            'name' => '',
                            'icon' => 'eye',
                            'action' => 'link',
                            'actionUrl' => '/profile/{id}',
                            'class' => 'btn btn-primary btn-sm me-1',
                            'description' => 'View user profile',
                        ],
                        [
                            'name' => '',
                            'icon' => 'pencil',
                            'action' => 'link',
                            'actionUrl' => '/admin/user/{id}/edit',
                            'class' => 'btn btn-warning btn-sm me-1',
                            'description' => 'Edit user',
                        ],
                        [
                            'name' => '',
                            'icon' => 'trash',
                            'action' => 'ajax',
                            'actionUrl' => '/api/v1/admin/user/{id}/delete',
                            'class' => 'btn btn-danger btn-sm',
                            'description' => 'Delete user',
                            'confirm' => 'Are you sure?',
                        ],
                    ],
                ],
            ]
        ];

        $this->requestParameters->getRequestValues(['page','filters','limit','order']);

        if (!empty($this->requestParameters->values->page)) {
            $page = (int)$this->requestParameters->values->page;
        } else {
            $page = 1;
        }

        // Filters
        $conditionsArr = [];
        $filters = [
            'search' => [
                'id' => 'search',
                'name' => 'Search',
                'type' => 'string',
                'value' => '',
            ],
            'role' => [
                'id' => 'role',
                'name' => 'Role',
                'type' => 'select',
                'value' => '',
                'array' => [
                    'all' => ['name' => 'All', 'value' => ''],
                    'user' => ['name' => 'User', 'value' => 'user'],
                    'moderator' => ['name' => 'Moderator', 'value' => 'moderator'],
                    'admin' => ['name' => 'Admin', 'value' => 'admin'],
                ],
            ],
            'status' => [
                'id' => 'status',
                'name' => 'Status',
                'type' => 'select',
                'value' => '',
                'array' => [
                    'all' => ['name' => 'All', 'value' => ''],
                    'created' => ['name' => 'Created', 'value' => 'created'],
                    'active' => ['name' => 'Active', 'value' => 'active'],
                    'deleted' => ['name' => 'Deleted', 'value' => 'deleted'],
                ],
            ],
        ];

        if (
            !empty($this->requestParameters->values->filters)
            && ($filtersArr = Strings::paramStringToJson($this->requestParameters->values->filters))
        )
        {
            foreach ($filtersArr as $filterKey => $filterValue) {
                if (isset($filters[$filterKey]) && !empty($filterValue['value'])) {

                    $filters[$filterKey]['value'] = $filterValue['value'];

                    $this->_changeConditionsArr($conditionsArr, $filterKey, $filterValue['value']);
                }
            }
        }

        // Order
        $orderQuery = [
            'col' => 'id',
            'order' => 'DESC',
        ];

        if (
            !empty($this->requestParameters->values->order)
            && ($orderArr = Strings::paramStringToJson($this->requestParameters->values->order))
            && !empty($orderArr['col'])
            && !empty($orderArr['order'])
        ) {
            $orderQuery = $orderArr;
        }

        $order = [$orderQuery['col'] => $orderQuery['order']];

        $offset = ($page - 1) * self::ROWS_COUNT;

        $userList = new UserList(
            $this->settings, $this->_makeConditions($conditionsArr), $order, $offset, (self::ROWS_COUNT + 1), 0, true
        );
        $users = $userList->getDtoFields($nullDto);

        if (count($users) === (self::ROWS_COUNT + 1)) {
            $out['nextPage'] = $page + 1;
            array_pop($users);
        } else {
            $out['nextPage'] = null;
        }

        $out['rows'] = $users;
        $out['order'] = $orderQuery;
        $out['filters'] = $filters;

        $this->renderJson($out, true);
    }

    /**
     * @param array $conditionsArr
     * @param string $filterKey
     * @param string $filterValue
     * @return void
     */
    private function _changeConditionsArr(array &$conditionsArr, string $filterKey, string $filterValue): void
    {
        if ($filterValue !== '') {
            $conditionsArr[$filterKey] = $filterValue;
        }
    }

    /**
     * @param array $conditionsArr
     * @return array
     */
    private function _makeConditions(array $conditionsArr): array
    {
        $conditions = [];

        if (!empty($conditionsArr['search'])) {
            $conditions['email'] = [
                'logic' => 'AND',
                'sign' => 'LIKE',
                'value' => '%' . mb_strtolower($conditionsArr['search'], 'UTF-8') . '%',
            ];
        }

        if (!empty($conditionsArr['role'])) {
            $conditions['role'] = $conditionsArr['role'];
        }

        if (!empty($conditionsArr['status'])) {
            $conditions['status'] = $conditionsArr['status'];
        }

        return $conditions;
    }
}