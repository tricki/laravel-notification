<?php

namespace Tricki\Notification\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Config;
use Event;

/**
 * Handles assigning notification to users
 *
 * @author Thomas Rickenbach
 * @package tricki/laravel-notification
 */
class NotificationUser extends Pivot
{

    use SoftDeletingTrait;

    protected $table = 'notification_user';
    protected $foreignKey = 'notification_id';
    protected $otherKey = 'user_id';
    protected $dates = ['deleted_at'];
    protected $visible = ['user_id', 'notification_id', 'created_at', 'updated_at',
        'read_at'];

    public function __construct($parent = NULL, $attributes = array(), $table = '', $exists = false)
    {
        if (!$parent || !is_a($parent, '\Illuminate\Database\Eloquent\Model'))
        {
            $parent = new Notification;
        }
        if (empty($table))
        {
            $table = $this->table;
        }
        parent::__construct($parent, $attributes, $table, $exists);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function($model)
        {
            $responses = Event::fire('notification::assigned', array($model));
        });

        static::saving(function($model)
        {
            $model->updateTimestamps();
        });
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
        return $query->whereNotNull('notification_user.read_at');
    }

    /**
     * Mark the user notification as read
     * 
     * @return \Tricki\Notification\Models\NotificationUser
     */
    public function setRead()
    {
        $this->read_at = new \DateTime();
        $this->save();

        return $this;
    }

    /**
     * Mark the user notification as unread
     * 
     * @return \Tricki\Notification\Models\NotificationUser
     */
    public function setUnread()
    {
        $this->read_at = NULL;
        $this->save();

        return $this;
    }

    public function render()
    {
        return $this->notification->render();
    }

}
