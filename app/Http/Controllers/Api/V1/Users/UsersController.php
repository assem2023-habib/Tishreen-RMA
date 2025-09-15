<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use ApiResponse;
    public function __invoke()
    {
        $users = User::select('id', 'user_name')
            ->get();
        if (empty($users))
            return $this->errorResponse(
                'No Users Found',
                HttpStatus::NOT_FOUND->value,
            );
        return $this->successResponse(
            ['users' => $users],
            'users name get successfully',
        );
    }
}
