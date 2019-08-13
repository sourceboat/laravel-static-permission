<?php

namespace Sourceboat\Permission\Middlewares;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        if (auth()->guest()) {
            abort(403);
        }

        $roles = collect(explode('|', $roles));
        $user = auth()->user();

        if (!$roles->contains($user->getRoleName())) {
            abort(403);
        }

        return $next($request);
    }

}
