<?php

namespace App\Http\Controllers\Api\V1\Authorization;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Authorization\StoreAuthorizationRequest;
use App\Services\AuthorizationService;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizationController extends Controller
{
    use ApiResponse;
    public function __construct(private AuthorizationService $authorizationService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authorizations =  $this->authorizationService->getAllAuthorization(Auth::user()->id);
        if (empty($authorizations))
            return $this->errorResponse(
                'no authorizations found',
                HttpStatus::FORBIDDEN->value,
            );
        return $this->successResponse(
            ['authorizations' => $authorizations],
            'get all authorizations for user',
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorizationRequest $request)
    {
        $data = $request->validated();
        $authorization = $this->authorizationService->createAuthorization($data);
        if (empty($authorizations))
            return $this->errorResponse(
                'cannot create authorization',
                HttpStatus::FORBIDDEN->value,
            );
        return $this->successResponse(
            ['authorizations' => $authorization],
            'create authorization success',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
