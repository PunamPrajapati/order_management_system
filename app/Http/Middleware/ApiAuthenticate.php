<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ApiAuthenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected function unauthenticated($request, array $guards)
    {
        // Return a JSON response instead of redirect
        abort(response()->json([
            'status' => false,
            'message' => 'Unauthenticated.',
        ], 401));
    }
}
