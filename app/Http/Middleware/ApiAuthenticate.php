<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        try {
            return $next($request);
        } catch (AuthenticationException $e) {
            return response()->json([
                'status' => false,
                'message' => __('auth.not_authenticated'),
                'errors' => null,
            ], 401);
        }
    }
}
