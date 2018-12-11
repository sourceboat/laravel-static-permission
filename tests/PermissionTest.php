<?php

namespace Sourceboat\Permission\Test;

class PermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->permissions = [
            'user/#',
            '!user/create',
            'test/+/+/test',
        ];

        $this->app['config']->set('permission.roles.user', $this->permissions);

        $this->user = User::create(['email' => 'test@user.com']);
    }

    public function testGetPermissions(): void
    {
        $this->user->assignRole('user');

        $this->assertEquals($this->user->getAllPermissions(), collect($this->permissions));
    }

    public function testGetPermissionsWithoutRole(): void
    {
        $this->assertEquals($this->user->getAllPermissions(), collect([]));
    }

    public function testHasAnyPermissionString(): void
    {
        $this->user->assignRole('user');

        $this->assertTrue($this->user->hasAnyPermission('user/edit'));
    }

    public function testHasAnyPermissionArray(): void
    {
        $this->user->assignRole('user');

        $this->assertTrue($this->user->hasAnyPermission(['user/edit', 'user/create']));
    }

    public function testHasPermissionToString(): void
    {
        $this->user->assignRole('user');

        $this->assertTrue($this->user->hasPermissionTo('user/edit'));
    }

    public function testHasPermissionToArray(): void
    {
        $this->user->assignRole('user');

        $this->assertFalse($this->user->hasPermissionTo(['user/edit', 'user/create']));
    }

    public function testHasPermissionToForbiddenRule(): void
    {
        $this->user->assignRole('user');

        $this->assertFalse($this->user->hasPermissionTo('user/create'));
    }

    public function testHasPermissionToNotDefined(): void
    {
        $this->user->assignRole('user');

        $this->assertFalse($this->user->hasPermissionTo('news/edit'));
    }

    public function testHasPermissionToMultipleWildcards(): void
    {
        $this->user->assignRole('user');

        $this->assertTrue($this->user->hasPermissionTo('test/testa/testb/test'));
    }

    public function testHasPermissionToWrongMultipleWildcards(): void
    {
        $this->user->assignRole('user');

        $this->assertFalse($this->user->hasPermissionTo('test/testa/test'));
    }

    public function testHasPermissionString(): void
    {
        $this->user->assignRole('user');

        $this->assertTrue($this->user->hasPermission('user/edit'));
    }

    public function testHasPermissionArray(): void
    {
        $this->user->assignRole('user');

        $this->assertFalse($this->user->hasPermission(['user/edit', 'user/create']));
    }

    public function testHasPermissionForbiddenRule(): void
    {
        $this->user->assignRole('user');

        $this->assertFalse($this->user->hasPermission('user/create'));
    }

    public function testHasPermissionNotDefined(): void
    {
        $this->user->assignRole('user');

        $this->assertFalse($this->user->hasPermission('news/edit'));
    }

    public function testHasPermissionMultipleWildcards(): void
    {
        $this->user->assignRole('user');

        $this->assertTrue($this->user->hasPermission('test/testa/testb/test'));
    }

    public function testHasPermissionWrongMultipleWildcards(): void
    {
        $this->user->assignRole('user');

        $this->assertFalse($this->user->hasPermission('test/testa/test'));
    }
}
