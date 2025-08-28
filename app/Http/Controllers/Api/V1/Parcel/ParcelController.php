<?php

namespace App\Http\Controllers\Api\V1\Parcel;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Parcel\{DeleteParcelRequest, StoreParcelRequest, UpdateParcelRequest};
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ParcelService;

class ParcelController extends Controller
{
    use ApiResponse;
    public function __construct(private ParcelService $parcelService) {}

    public function index()
    {
        $parcels = $this->parcelService->showParcels(Auth::user()->id);
        if ($parcels->isEmpty())
            return $this->errorResponse(
                __('parcel.no_parcels_found'),
                HttpStatus::NOT_FOUND->value,
            );
        return $this->successResponse(
            [
                'parcels' => $parcels,
            ],
            message: "all Parcels for the user : " . Auth::user()->user_name,
        );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParcelRequest $request)
    {
        try {
            $data = $request->validated();
            $parcel = $this->parcelService->createParcel($data);
            if (empty($parcel))
                return $this->errorResponse(
                    'create Parcel field!.',
                    HttpStatus::UNPROCESSABLE_ENTITY->value,
                );
            return $this->successResponse(
                ['parcel' => $parcel],
                'parcel created successfuly',
                HttpStatus::CREATED->value,
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'error in server',
                HttpStatus::INTERNET_SERVER_ERROR->value,
                $e->getMessage(),
            );
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $parcel = $this->parcelService->showParcel(Auth::user()->id, $id);
        if (empty($parcel))
            return $this->errorResponse(
                __('parcel.no_parcel_found'),
                HttpStatus::NOT_FOUND->value,
            );
        return $this->successResponse(
            ['parcel' => $parcel],
            __('parcel.parcel_found'),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParcelRequest $request, string $id)
    {
        $data = $request->validated();

        $parcel = $this->parcelService->updateParcel($id, $data);
        if (empty($parcel)) {
            return $this->errorResponse(
                __('parcel.no_parcel_found'),
                HttpStatus::NOT_FOUND->value,
            );
        }
        return $this->successResponse(
            ['parcel' => $parcel],
            __('parcel.parcel_updated_successfuly'),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteParcelRequest $request)
    {
        $id = $request->validated()['id'];
        $parcel = $this->parcelService->deleteParcel(Auth::user()->id, $id);
        if (empty($parcel))
            return $this->errorResponse(
                __('parcel.no_parcel_found'),
                HttpStatus::NOT_FOUND->value,
            );
        return $this->successResponse(
            [],
            __('parcel.parcel_delete_successfuly'),
        );
    }
}
