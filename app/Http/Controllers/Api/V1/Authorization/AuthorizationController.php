<?php

namespace App\Http\Controllers\Api\V1\Authorization;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Authorization\StoreAuthorizationRequest;
use App\Http\Requests\Api\V1\Authorization\UpdateAuthorizationRequest;
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
        if ($authorizations->isEmpty())
            return $this->errorResponse(
                __('authorization.no_authorizations_granted_by_you'),
                HttpStatus::FORBIDDEN->value,
            );
        return $this->successResponse(
            [
                'authorizations' => $authorizations->load('parcel', 'authorizedUser')
            ],
            message: __('authorization.authorizations_retrieved_successfully'),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorizationRequest $request)
    {
        $data = $request->validated();
        $authorization = $this->authorizationService->createAuthorization($data);
        if (empty($authorization))
            return $this->errorResponse(
                'cannot create authorization',
                HttpStatus::FORBIDDEN->value,
            );
        return $this->successResponse(
            [
                'authorization' => $authorization->fresh()
            ],
            'create authorization success',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $authorization = $this->authorizationService->showAuthorization($id);
        if (empty($authorization)) {
            return $this->errorResponse(
                __('authorization.no_authorization_found'),
                HttpStatus::NOT_FOUND->value
            );
        }

        return $this->successResponse(
            ['authorization' => $authorization],
            __('authorization.authorization_retrieved_successfully')
        );
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorizationRequest $request, string $id)
    {
        $data = $request->validated();
        $authorization = $this->authorizationService->updateAuthorization($id, $data);

        if (!$authorization) {
            return $this->errorResponse(
                __('authorization.cannot_update_authorization'),
                HttpStatus::FORBIDDEN->value
            );
        }

        return $this->successResponse(
            ['authorization' => $authorization],
            __('authorization.authorization_updated_successfully')
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->authorizationService->deleteAuthorization($id);

        if (!$deleted) {
            return $this->errorResponse(
                __('authorization.no_authorizations_found'),
                HttpStatus::NOT_FOUND->value
            );
        }

        return $this->successResponse(
            [],
            __('authorization.authorization_deleted_successfully')
        );
    }
}
