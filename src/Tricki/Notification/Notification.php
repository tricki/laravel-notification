<?php

namespace Tricki\Notification;

use Config;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification
 *
 * @author Thomas Rickenbach
 * @package tricki/laravel-notification
 */
class Notification
{

    public function getClass($type)
    {
        if (empty($type))
        {
            throw new \Exception('No notification type given');
        }
        $namespace = Config::get('laravel-notification::namespace');
        $namespace = join('\\', explode('\\', $namespace)) . '\\';

        return $namespace . studly_case($type) . 'Notification';
    }

    /**
     * Creates a notification and assigns it to some users
     *
     * @param string $type  The notification type
     * @param Model $sender The object that initiated the notification (a user, a group, a web service etc.)
     * @param Model|NULL $object An object that was changed (a post that has been liked).
     * @param mixed $users The user(s) which should receive this notification.
     * @param mixed|NULL $data Any additional data
     *
     * @return \Tricki\Notification\Models\Notification
     */
    public function create($type, Model $sender, Model $object = NULL, $users = array(), $data = NULL)
    {
        $class = $this->getClass($type);
        $notification = new $class();

        if ($data)
        {
            $notification->data = $data;
        }
        $notification->sender()->associate($sender);
        if ($object)
        {
            $notification->object()->associate($object);
        }
        $notification->save();

        $notification_users = array();
        foreach ($users as $user)
        {
            $notification_user = new Models\NotificationUser($notification);
            $notification_user->user_id = $user->id;
            $notification_users[] = $notification_user;
        }
        $notification->users()->saveMany($notification_users);

        return $notification;
    }

}
