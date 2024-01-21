<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User subscribers entity list class
 */

namespace iceCMS2\Models;

use iceCMS2\Tools\Exception;

class UserSubscriberList extends AbstractEntityList
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'user_subscribers';

    /**
     * User subscriptions list
     *
     * @param int $userId
     * @param array $order
     * @return array
     * @throws Exception
     */
    public function getSubscriptions(int $userId, array $order = ['date_add' => 'DESC']): array
    {
        $this->_conditions = [
            'parent_id' => $userId,
        ];
        $this->_order = $order;
        return $this->get();
    }

    /**
     * User subscribers list
     *
     * @param int $userId
     * @param array $order
     * @return array
     * @throws Exception
     */
    public function getSubscribers(int $userId, array $order = ['date_add' => 'DESC']): array
    {
        $this->_conditions = [
            'child_id' => $userId,
        ];
        $this->_order = $order;
        return $this->get();
    }
}