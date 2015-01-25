<?php

namespace Tricki\Notification;

use Illuminate\Database\Eloquent\Model;
use Tricki\Notification\Models\Notification as NotificationModel;

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

		return $namespace . camel_case($type) . 'Notification';
	}

	public function create($type, Model $sender, Model $object = NULL, $users = array(), $data = NULL)
	{
		$class = Notification::getClass($type);
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
