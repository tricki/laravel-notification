<?php

namespace Tricki\Notification\Models;

use Eloquent;

/**
 * The main Notification class
 *
 * @author Thomas Rickenbach
 */
class Notification extends AbstractEloquent
{

	protected $isSuperType = true;

	protected $table = 'notifications';

	public static $type = '';

	public function users()
	{
		return $this->belongsToMany('User', 'notification_user', 'notification_id')->withTimestamps();
	}

	public function sender()
	{
		return $this->morphTo();
	}

	public function object()
	{
		return $this->morphTo();
	}

	public function scopeUnread($query)
	{
		return $query->wherePivot('read_at', NULL);
	}

	public function scopeRead($query)
	{
		return $query->wherePivotNot('read_at', NULL);
	}

	public function newPivot(Eloquent $parent, array $attributes, $table, $exists)
	{
		return new NotificationUser($parent, $attributes, $table, $exists);
	}

	protected function isSubType()
	{
		return get_class() !== get_class($this);
	}

	protected function getClass($type)
	{
		return \Notification::getClass($type);
	}

	protected function getType()
	{
		return static::$type;
	}

}
