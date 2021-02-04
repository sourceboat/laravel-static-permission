# laravel-static-permission

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sourceboat/laravel-static-permission.svg?style=flat-square)](https://packagist.org/packages/sourceboat/laravel-static-permission)
[![Build Status](https://img.shields.io/travis/sourceboat/laravel-static-permission/master.svg?style=flat-square)](https://travis-ci.org/sourceboat/laravel-static-permission)
[![Total Downloads](https://img.shields.io/packagist/dt/sourceboat/laravel-static-permission.svg?style=flat-square)](https://packagist.org/packages/sourceboat/laravel-static-permission)

Manage user permissions and roles in your Laravel application by domain driven rules.

* [Installation](#installation)
* [Usage](#usage)
  * [Using roles](#using-roles)
  * [Using permissions](#using-permissions)
  * [Using Blade directives](#using-blade-directives)
* [Config](#config)

## Example

```php
$user->assignRole('admin');

$user->hasRole('admin'); // true
```

Define roles and permissions in `config/permission.php`.

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

Check permissions by

```php
$admin->hasPermission('news/delete'); // true
$editor->hasPermission('news/delete'); // false
$user->hasPermission('news/delete'); // false
```

## Installation

```bash
composer require sourceboat/laravel-static-permission
```

## Usage

### Add trait to model

```php
  use HasRoles;
```

### Using roles

Define roles in `config/permission.php`.

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

Check role via:

```php
$model->hasRole('admin');

$model->getRoleName(); // return admin
```

### Using permissions

Permissions are based on the MQTT syntax and specified as path. Thus, individual security levels can be mapped and generally released via wildcards.

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

Define roles and permissions in `config/permission.php`.

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

Use Blade directives in your views.

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

Use several permissions.

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
use Sourceboat\Permission\Middlewares\RoleMiddleware;
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

See [releases](https://github.com/sourceboat/laravel-static-permission/releases) for details.

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
