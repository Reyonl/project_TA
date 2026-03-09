<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (auth()->guard('admin')->check()) {
            $user = auth()->guard('admin')->user();
            if (in_array($user->role, $roles)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
