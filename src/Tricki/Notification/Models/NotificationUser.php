<?php

namespace Tricki\Notification\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Config;

/**
 * Description of Notification
 *
 * @author Thomas
 */
class NotificationUser extends Pivot
{

	use SoftDeletingTrait;

	protected $table = 'notification_user';
	protected $dates = ['deleted_at'];
	protected $visible = ['user_id', 'notification_id', 'created_at', 'updated_at',
		'read_at'];

	public function __construct($parent = NULL, $attributes = array(), $table = '', $exists = false)
	{
		if(!$parent || !is_a($parent, '\Illuminate\Database\Eloquent\Model'))
		{
			$parent = new Notification;
		}
		if(empty($table))
		{
			$table = $this->table;
		}
		parent::__construct($parent, $attributes, $table, $exists);
	}
	public function user()
	{
		return $this->belongsTo(Config::get('auth.model'));
	}

	public function notification()
	{
		return $this->belongsTo('Tricki\Notification\Models\Notification');
	}

	public function scopeUnread($query)
	{
		return $query->where('notification_user.read_at', NULL);
	}

	public function scopeRead($query)
	{
		return $query->whereNotNull('user_notifications.read_at');
	}
        
        public function setRead()
        {
            $this->read_at = new \DateTime();
            $this->save();
            
            return $this;
        }

}
