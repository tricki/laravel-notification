<?php

namespace Tricki\Notification;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Notification
 *
 * @author Thomas
 */
class Notification
{

	public function getClass($type)
	{
		if (empty($type))
		{
			throw new \Exception('No notification type given');
		}
		$namespace = \Illuminate\Support\Facades\Config::get('notification::namespace');
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
		$notification->users()->saveMany($users);

		return $notification;
	}

}
