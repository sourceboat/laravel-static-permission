<?php

namespace Sourceboat\Middleware\Test;

use Sourceboat\Middleware\RoleMiddleware;
use Sourceboat\Permission\Test\TestCase;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MiddlewareTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->roleMiddleWare = new RoleMiddleware($this->app);

        $this->app['config']->set('permission.roles.admin', [
            'users/*',
        ]);

        $this->user = User::create(['email' => 'test@user.com']);
    }

    public function testGustCannotAccessProtectedRoute()
    {
        $this->assertEquals(
            $this->runMiddleware(
                $this->roleMiddleWare, 'testRole'
            ), 403);

    }

    public function testUserCanAccessRoleIfHaveThisRole()
    {
        $this->user->assignRole('testRole');
        Auth::login($this->user);

        $this->assertEquals(
            $this->runMiddleware(
                $this->roleMiddleware, 'testRole'
            ), 200);
    }

    protected function runMiddleware($middleware, $parameter)
    {
        try {
            return $middleware->handle(new Request(), function() {
                return (new Response())->SetContent('<html></html>');
            }, $parameter)->status();
        } catch (HttpException $e) {
            return $e->getStatusCode();
        }
    }
}
