Define permissions by code
====================================

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sourceboat/laravel-static-permission.svg?style=flat-square)](https://packagist.org/packages/sourceboat/laravel-static-permission)
[![Build Status](https://img.shields.io/travis/sourceboat/laravel-static-permission/master.svg?style=flat-square)](https://travis-ci.org/sourceboat/laravel-static-permission)
[![Total Downloads](https://img.shields.io/packagist/dt/sourceboat/laravel-static-permission.svg?style=flat-square)](https://packagist.org/packages/sourceboat/laravel-static-permission)

* [Installation](#installation)
* [Usage](#usage)
  * [Usign roles](#using-roles)
  * [Usign permissions](#using-permissions)
  * [Using Blade directives](#using-blade-directives)
* [Config](#config)

This package allows you to manage user permissions and roles by domain driven rules.

```php
$user->assignRole('admin');

$user->hasRole('admin'); // true
```

You can define roles and permissions by code at `config/permission.php`.

```php
'role' => [
  'admin' => [
    'news/#', // Allow all paths beginning with news/
  ],
  'editor' => [
    'news/#',
    '!news/delete', // Explicitly forbid news/delete
  ],
  'user' => [
    'news/show', // Explicitly allow news/show
  ],
]
```

You can check permissions by

```php
$admin->hasPermission('news/delete'); // true
$editor->hasPermission('news/delete'); // false
$user->hasPermission('news/delete'); // false
```

## Installation

```bash
composer require sourceboat/laravel-static-permission
```

Older than Laravel 5.5 need a service provider registration.

```php
// config/app.php

'providers' => [
  Sourceboat\Permission\PermissionServiceProvider::class,
];
```

```php
php artisan vendor:publish
```

## Usage

### Add trait to model

```php
  use HasRoles;
```

### Using roles

You can define the roles in the `config/permission.php` file.

```php
// config/permission.php

'roles' => [
  'role_name' => [],
  'admin' => [],
],
```
#### Assign role

Add a role to a model.

```php
$model->assignRole('admin');
```

#### Check role

You can check the role via:

```php
$model->hasRole('admin');

$model->getRoleName(); // return admin
```

### Using permissions

Permissions are based on the MQTT syntax. Permissions are specified as path. Thus, individual security levels can be mapped and generally released via wildcards.

#### Check permissions

```php
$model->hasPermission('users/show/email');
```

```php
$model->hasPermission(['users/show', 'users/edit']);
```

```php
$model->hasAnyPermission('users/show/email');
```

```php
$model->hasAnyPermission(['users/show', 'users/edit']);
```

#### Configuration

- `+` Wildcard for one level
- `#` Wildcard for everything following
- `!` Before the permission - prohibits permission

You can define the role permissions in the `config/permission.php` file.

```php
// config/permission.php

'roles' => [
  'role_name' => [
    'users/+/foo'
  ],
  'admin' => [
    'users/#',
    '!users/create',
  ],
],
```

### Using Blade directives

You can use Blade directives in your views.

#### Role

```blade
@role('admin')
  Show if user is admin
@endrole
```

```blade
@unlessrole('admin')
  Show if user is not admin
@endunlessrole
```

#### Permission

```blade
@permission('user/edit')
  Show if user has rights to user/edit
@endpermission
```

You can use several permissions too.

```blade
@permission('user/edit|user/create')
  Show if user has rights to user/edit AND user/create
@endpermission
```

```blade
@anypermission('user/edit|user/create')
 Show if user has rights to user/edit OR user/create
@endanypermission
```

#### Middleware
Add the middleware to your `src/Http/Kernel.php`
```php
use Sourceboat\Middleware\RoleMiddleware;
class Kernel extends HttpKernel
{
... 
  protected $routeMiddleware = [
    ...
    'role' => RoleMiddleware::class
  ]

}
```

And use it like 
```php
Route::group(['middleware' => ['role:admin']], function () {
    //
})

```

## Config

Example Config

```php
<?php
// config/permission.php

return [
    /**
     * Column name of the model
     */
    'column_name' => 'role',

    /**
     * Roles with permissions
     *
     * - `+` Wildcard one level
     * - `#` Wildcard everything following
     * - `!` Before the permission - prohibits permission
     *
     * 'admin' => [
     *     'users/#',
     *     'users/+/field',
     *     '!users/create'
     * ]
     */
    'roles' => [],

];

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for details.

## Contributing

```bash
composer lint:phpcs
composer lint:phpmd
```

## Credits

This package is heavily inspired by [Spatie / laravel-permission](https://github.com/spatie/laravel-permission).

- [Philipp Kübler](https://github.com/pkuebler)
- [All Contributors](https://github.com/sourceboat/laravel-static-permission/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
