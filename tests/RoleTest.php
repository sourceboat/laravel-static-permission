<?php

namespace Sourceboat\Permission\Tests;

class RoleTest extends TestCase
{
    public function testAssignExistentRole(): void
    {
        $this->user->assignRole('admin');

        $this->assertEquals($this->user->role, 'admin');
    }

    public function testAssignNonExistentRole(): void
    {
        $this->app['config']->set('permission.roles.admin', []);

        $this->user->assignRole('lolo');

        $this->assertEquals($this->user->role, null);
    }

    public function testHasRole(): void
    {
        $this->user->role = 'admin';
        $this->user->save();

        $this->assertTrue($this->user->hasRole('admin'));
    }

    public function testHasNotRole(): void
    {
        $this->user->role = 'admin';
        $this->user->save();

        $this->assertFalse($this->user->hasRole('lolo'));
    }

    public function testGetSetRoleName(): void
    {
        $this->user->role = 'admin';
        $this->user->save();

        $this->assertEquals($this->user->getRoleName(), 'admin');
    }

    public function testGetUnsetRoleName(): void
    {
        $this->assertEquals($this->user->getRoleName(), null);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('permission.roles.admin', [
            'users/*',
        ]);

        $this->user = User::create(['email' => 'test@user.com']);
    }
}
