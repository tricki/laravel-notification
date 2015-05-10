<?php

namespace Tricki\Notification\Models;

use Event;

/**
 * The main Notification class
 *
 * @author Thomas Rickenbach
 * @package tricki/laravel-notification
 */
class Notification extends AbstractEloquent
{

    protected $isSuperType = true;
    protected $table = 'notifications';
    public static $type = '';

    public static function boot()
    {
        parent::boot();

        static::created(function($model)
        {
            $responses = Event::fire('notification::created', array($model));
        });
    }

    public function users()
    {
        return $this->hasMany('Tricki\Notification\Models\NotificationUser', 'notification_id');
    }

    public function newPivot(\Eloquent $parent, array $attributes, $table, $exists)
    {
        return new NotificationUser($parent, $attributes, $table, $exists);
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
        return $query->where('read_at', NULL);
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
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
