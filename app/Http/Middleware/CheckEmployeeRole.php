<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\RoleName;
use App\Enums\HttpStatus;
use App\Trait\ApiResponse;

class CheckEmployeeRole
{
    use ApiResponse;

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || (!$user->hasRole(RoleName::EMPLOYEE->value) && !$user->hasRole(RoleName::SUPER_ADMIN->value))) {
            return $this->errorResponse(
                'Access denied. Employee or Admin role required.',
                HttpStatus::FORBIDDEN->value
            );
        }

        return $next($request);
    }
}
