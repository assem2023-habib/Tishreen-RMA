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
            ->paginate(10);
        
        if ($users->isEmpty())
            return $this->errorResponse(
                'No Users Found',
                HttpStatus::NOT_FOUND->value,
            );
            
        return $this->successResponse(
            ['users' => $users],
            'users name get successfully',
        );
    }

    public function search(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|min:1',
        ]);

        $userName = $request->query('user_name');

        $users = User::select('id', 'user_name')
            ->where('user_name', 'like', "%{$userName}%")
            ->paginate(10);

        if ($users->isEmpty()) {
            return $this->errorResponse(
                'No Users Found Matching: ' . $userName,
                HttpStatus::NOT_FOUND->value,
            );
        }

        return $this->successResponse(
            ['users' => $users],
            'Users retrieved successfully'
        );
    }
}
