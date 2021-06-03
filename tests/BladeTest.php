<?php

namespace Sourceboat\Permission\Test;

use Artisan;

class BladeTest extends TestCase
{
    public function testPermission(): void
    {
        $this->user->assignRole('admin');
        $this->be($this->user);

        $this->assertEquals($this->renderView('permission', ['permission' => 'users/edit']), 'Test');
        $this->assertEquals($this->renderView('permission', ['permission' => 'users/create']), '');

        $this->assertEquals($this->renderView('permission', [
            'permission' => 'users/edit|users/create',
        ]), '');
        $this->assertEquals($this->renderView('permission', [
            'permission' => 'users/edit|users/delete',
        ]), 'Test');
    }

    public function testAnyPermission(): void
    {
        $this->user->assignRole('admin');
        $this->be($this->user);

        $this->assertEquals($this->renderView('anypermission', ['permission' => 'users/edit']), 'Test');
        $this->assertEquals($this->renderView('anypermission', ['permission' => 'users/create']), '');

        $this->assertEquals($this->renderView('anypermission', [
            'permission' => 'users/edit|users/create',
        ]), 'Test');
    }

    public function testRole(): void
    {
        $this->user->assignRole('admin');
        $this->be($this->user);

        $this->assertEquals($this->renderView('role', ['role' => 'admin']), 'Test');
        $this->assertEquals($this->renderView('role', ['role' => 'user']), '');
    }

    public function testUnlessRole(): void
    {
        $this->user->assignRole('admin');
        $this->be($this->user);

        $this->assertEquals($this->renderView('unlessrole', ['role' => 'admin']), '');
        $this->assertEquals($this->renderView('unlessrole', ['role' => 'user']), 'Test');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('permission.roles.admin', [
            'users/#',
            '!users/create',
        ]);

        $this->user = User::create(['email' => 'test@user.com']);
    }

    /**
     * from Spatie\Permission\Test;
     * return compiled blade views
     *
     * @param string $view view name
     * @param string[] $parameters vars can used in the view
     */
    protected function renderView(string $view, array $parameters): string
    {
        Artisan::call('view:clear');

        if (is_string($view)) {
            $view = view($view)->with($parameters);
        }

        return trim((string) $view);
    }
}
