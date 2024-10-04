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

use desfpc\Visualijoper\Visualijoper;
use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\DTO\FilesListAdminDto;
use iceCMS2\Helpers\Strings;
use iceCMS2\Locale\LocaleText;
use iceCMS2\Models\File;
use iceCMS2\Models\FileList;
use iceCMS2\Models\User;
use iceCMS2\Models\UserList;
use iceCMS2\Tools\Exception;

class AdminFiles extends AbstractController implements ControllerInterface
{
    private const ROWS_COUNT = 20;
    private const EDITABLE_PROPS = [];

    /**
     * Delete File by ID
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

        //TODO Delete File
    }

    /**
     * Get File by ID for editing
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

        //TODO Get File by ID
    }

    /**
     * @return ?File
     * @throws Exception
     */
    private function _checkUserFromRequest(): ?File
    {
        if (!isset($this->routing->pathInfo['query_vars']['id'])) {
            $this->renderJson(['message' => 'No File ID passed'], false);
            return null;
        }

        $id = (int)$this->routing->pathInfo['query_vars']['id'];
        $file = new File($this->settings);
        if (!$file->load($id)) {
            $this->renderJson(['message' => 'Wrong File ID'], false);
            return null;
        }

        return $file;
    }

    /**
     * Admin Files list
     *
     * @return void
     * @throws Exception
     */
    public function list(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $nullDto = new FilesListAdminDto();

        // Columns
        $out = [
            'cols' => [
                [
                    'id' => 'id',
                    'name' => LocaleText::get($this->settings, 'files/fields/id', [], $this->settings->locale),
                    'ordered' => true,
                    'action' => 'link',
                    'actionUrl' => '/admin/files/{id}/edit/',
                    'icon' => 'arrow-return-right',
                ],
                [
                    'id' => 'name',
                    'name' => LocaleText::get($this->settings, 'files/fields/name', [], $this->settings->locale),
                    'ordered' => true,
                    'editable' => true,
                    'editUrl' => '/api/v1/admin/files/{id}/edit-prop',
                    'inputType' => 'input',
                ],
                [
                    'id' => 'filename',
                    'name' => LocaleText::get($this->settings, 'files/fields/filename', [], $this->settings->locale),
                    'ordered' => true,
                    'editable' => true,
                    'editUrl' => '/api/v1/admin/files/{id}/edit-prop',
                    'inputType' => 'input',
                ],
                [
                    'id' => 'filetype',
                    'name' => LocaleText::get($this->settings, 'files/fields/filetype', [], $this->settings->locale),
                    'ordered' => true,
                ],
                [
                    'id' => 'size',
                    'name' => LocaleText::get($this->settings, 'files/fields/size', [], $this->settings->locale),
                    'ordered' => true,
                ],
                [
                    'id' => 'image_width',
                    'name' => LocaleText::get($this->settings, 'files/fields/image_width', [], $this->settings->locale),
                    'ordered' => true,
                ],
                [
                    'id' => 'image_height',
                    'name' => LocaleText::get($this->settings, 'files/fields/image_height', [], $this->settings->locale),
                    'ordered' => true,
                ],
                [
                    'id' => 'private',
                    'name' => LocaleText::get($this->settings, 'files/fields/private', [], $this->settings->locale),
                    'ordered' => true,
                ],
                [
                    'id' => 'created_time',
                    'name' => LocaleText::get($this->settings, 'files/fields/created_time', [], $this->settings->locale), //TODO make one file for common fields
                    'ordered' => true,
                ],
                [
                    'id' => 'actions',
                    'name' => 'Actions',
                    'ordered' => false,
                    'buttons' => [
                        [
                            'name' => '',
                            'icon' => 'save',
                            'action' => 'link',
                            'actionUrl' => '{url}',
                            'class' => 'btn btn-primary btn-sm me-1',
                            'description' => 'Save or view file',
                        ],
                        [
                            'name' => '',
                            'icon' => 'pencil',
                            'action' => 'link',
                            'actionUrl' => '/admin/files/{id}/edit',
                            'class' => 'btn btn-warning btn-sm me-1',
                            'description' => 'Edit file',
                        ],
                        [
                            'name' => '',
                            'icon' => 'trash',
                            'action' => 'ajax',
                            'actionUrl' => '/api/v1/admin/files/{id}/delete',
                            'class' => 'btn btn-danger btn-sm',
                            'description' => 'Delete file',
                            'confirm' => 'Are you sure?',
                        ],
                    ],
                ],
            ],
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
            'filetype' => [
                'id' => 'filetype',
                'name' => 'Type',
                'type' => 'select',
                'value' => '',
                'array' => [
                    'all' => ['name' => 'All', 'value' => ''],
                    'file' => ['name' => 'File', 'value' => 'file'],
                    'image' => ['name' => 'Image', 'value' => 'image'],
                    'document' => ['name' => 'Document', 'value' => 'document'],
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

        $filesList = new FileList(
            $this->settings, $this->_makeConditions($conditionsArr), $order, $offset, (self::ROWS_COUNT + 1), 0, true
        );

        $files = $filesList->getDtoFields($nullDto);

        if (is_array($files) && count($files) === (self::ROWS_COUNT + 1)) {
            $out['nextPage'] = $page + 1;
            array_pop($files);
        } else {
            $out['nextPage'] = null;
        }

        $out['rows'] = $files;
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
    private function _makeConditions(array $conditionsArr): array //TODO DRY - create abstract parent class
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