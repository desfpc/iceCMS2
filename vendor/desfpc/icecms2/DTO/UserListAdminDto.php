<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User List Admin DTO class
 */

namespace iceCMS2\DTO;

/**
 * @property int $id
 * @property string $email
 * @property string $role
 * @property string $status
 * @property string $createdTime
 */
class UserListAdminDto extends AbstractDto implements DtoInterface
{
    /** @var array DTO params */
    public const PARAMS = [
        'id',
        'email',
        'role',
        'status',
        'created_time',
    ];

    /**
     * Additional data setter
     *
     * @param array $data
     * @return void
     */
    protected function _setAdditionalData(array $data): void
    {
        // do nothing
    }
}