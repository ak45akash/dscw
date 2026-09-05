<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->canAccessAdmin()) {
            if ($request->expectsJson()) {
                abort(403, 'Unauthorized.');
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
