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

	public function user()
	{
		return $this->belongsTo(Config::get('auth.model'));
	}

	public function notification()
	{
		return $this->belongsTo('Tricki\Notification\Models\Notification');
	}

}
