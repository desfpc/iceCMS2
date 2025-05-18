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

    /** @var array|null columns for ID */
    protected ?array $_idColumns = ['user_id', 'target_id'];

    /**
     * User subscriptions list
     *
     * @param int $userId
     * @param array $order
     * @return bool|array
     * @throws Exception
     */
    public function getSubscriptions(int $userId, array $order = ['created_time' => 'DESC']): bool|array
    {
        $this->_conditions = [
            'user_id' => $userId,
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
            'target_id' => $userId,
        ];
        $this->_order = $order;
        return $this->get();
    }

    /**
     * Get mutual subscribers
     *
     * @param int $userId
     * @param int $subscriberId
     *
     * @return array
     * @throws Exception
     */
    public function getMutualSubscribers(int $userId, int $subscriberId): array
    {
        $ubscriptions = $this->getSubscriptions($userId);
        $subscribers = $this->getSubscribers($subscriberId);

        $mutual = [];
        foreach ($ubscriptions as $subscription) {
            foreach ($subscribers as $subscriber) {
                if ($subscription['target_id'] === $subscriber['user_id']) {
                    $mutual[] = $subscription;
                }
            }
        }

        return $mutual;
    }

    /**
     * Get non-mutual subscribers
     *
     * @param int $userId
     * @param int $subscriberId
     *
     * @return array
     * @throws Exception
     */
    public function getNonMutualSubscribers(int $userId, int $subscriberId): array
    {
        $ubscriptions = $this->getSubscriptions($userId);
        $subscribers = $this->getSubscribers($subscriberId);

        $nonMutual = [];
        foreach ($ubscriptions as $subscription) {
            $isMutual = false;
            foreach ($subscribers as $subscriber) {
                if ($subscription['target_id'] === $subscriber['user_id']) {
                    $isMutual = true;
                }
            }
            if (!$isMutual) {
                $nonMutual[] = $subscription;
            }
        }

        return $nonMutual;
    }
}