Laravel 4 Notification
======

A basic starting point for a flexible user notification system in Laravel 4.

It is easily extendable with new notification types and leaves rendering completely up to you.

This package only provides an extendable notification system without any controllers or views
since they are often very use case specific.

I'm open to ideas for extending this package.

## Installation

### 1. Install with Composer

```bash
composer require tricki/laravel-notification:@dev
```

This will update `composer.json` and install it into the `vendor/` directory.

(See the [Packagist website](https://packagist.org/packages/tricki/laravel-notification) for a list of available version numbers and
development releases.)

### 2. Add to Providers in `config/app.php`

```php
    'providers' => [
        // ...
        'Tricki\Notification\NotificationServiceProvider',
    ],
```

This registers the package with Laravel and automatically creates an alias called
`Notification`.

### 3. Publishing config

If your models are namespaced you will have to declare this in the package configuration.

Publish the package configuration using Artisan:

```bash
php artisan config:publish tricki/laravel-notification
```

Set the `namespace` property of the newly created `app/config/packages/tricki/laravel-notification/config.php`
to the namespace of your notification models.

#### Example

```php
'namespace' => '\MyApp\Models\'
```

### 4. Executing migration

```bash
php artisan migrate --package="tricki/laravel-notification"
```

### 5. Adding relationship to User

Extend your User model with the following relationship:

```php

	public function notifications()
	{
		return $this->hasMany('\Tricki\Notification\Models\NotificationUser');
	}

```

## Usage

### 1. Define notification models

You will need separate models for each type of notification. Some examples would
be `PostLikedNotification` or `CommentPostedNotification`.

These models define the unique behavior of each notification type like it's actions
and rendering.

A minimal notification model looks like this:

```php
<?php

class PostLikedNotification extends \Tricki\Notification\Models\Notification
{
	public static $type = 'post_liked';
}

```

The type will be saved in the database to differentiate between different
types. The class name **must** be the CamelCase version of this type and
end with "Notification".

> Remember to add the namespace of your notification models to this package's `config.php`.

### 2. Create a notification

Notifications can be created using `Notification::create`.

The function takes 5 parameters:

 * **$type** string
   The notification type (see [Define notification models](#1-define-notification-models))
 * **$sender** Model
   The object that initiated the notification (a user, a group, a web service etc.)
 * **$object** Model | NULL
   An object that was changed (a post that has been liked).
 * **$users** array | Collection | User
   The user(s) which should receive this notification.
 * **$data** mixed | NULL
   Any additional data you want to attach. This will be serialized into the database.

### 3. Retrieving a user's notifications

You can get a collection of notifications sent to a user using the `notifications` relationship,
which will return a collection of your notification models.

You can easily get a collection of all notifications sent to a user:

```php
$user = User::find($id);
$notifications = $user->notifications;
```

You can also only get read or unread notifications using the `read` and `unread` scopes respectively:

```php
$readNotifications = $user->notifications()->read()->get();
$unreadNotifications = $user->notifications()->unread()->get();
```

Since the notifications are instances of your own models you can easily have different behavior or
output for each notification type.

#### Example:

```php
<?php

class PostLikedNotification extends \Tricki\Notification\Models\Notification
{
	public static $type = 'post_liked';

	public function render()
	{
		return 'this is a post_liked notification';
	}
}

class CommentPostedNotification extends \Tricki\Notification\Models\Notification
{
	public static $type = 'comment_posted';

	public function render()
	{
		return 'this is a comment_posted notification';
	}
}
?>
```

```html
// notifications.blade.php

<ul>
	@foreach($user->notifications as $notification)
	<li>{{ $notification->render() }}</li>
	@endforeach
</ul>
```

This could output:
```html
<ul>
	<li>this is a post_liked notification</li>
	<li>this is a comment_posted notification</li>
</ul>
```