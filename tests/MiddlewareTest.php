<?php

namespace Sourceboat\Permission\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Sourceboat\Permission\Middlewares\RoleMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MiddlewareTest extends TestCase
{
    public function testGustCannotAccessProtectedRoute(): void
    {
        $this->assertEquals($this->runMiddleware($this->roleMiddleware, 'admin'), 403);
    }

    public function testUserCanAccessRoleIfHaveThisRole(): void
    {
        $this->user->assignRole('admin');
        Auth::login($this->user);

        $this->assertEquals($this->runMiddleware($this->roleMiddleware, 'admin'), 200);
    }

    public function testUserCantAccessRoleIfHavenotRole(): void
    {
        $this->user->assignRole('testRole');
        Auth::login($this->user);

        $this->assertEquals($this->runMiddleware($this->roleMiddleware, 'admin'), 403);
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->roleMiddleware = new RoleMiddleware($this->app);

        $this->app['config']->set('permission.roles.admin', [
            'users/*',
        ]);

        $this->user = User::create(['email' => 'test@user.com']);
    }

    protected function runMiddleware(RoleMiddleware $middleware, string $parameter): int
    {
        try {
            return $middleware->handle(new Request(), static function () {
                return (new Response())->SetContent('<html></html>');
            }, $parameter)->status();
        } catch (HttpException $e) {
            return $e->getStatusCode();
        }
    }
}
