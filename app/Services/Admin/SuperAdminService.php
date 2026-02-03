<?php

namespace App\Services\Admin;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use App\Models\Parcel;
use App\Enums\RoleName;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class SuperAdminService
{
    public function getSystemStats()
    {
        return [
            'total_parcels' => Parcel::count(),
            'total_branches' => Branch::count(),
            'total_employees' => Employee::count(),
            'total_users' => User::count(),
            'recent_parcels' => Parcel::latest()->take(5)->get(),
        ];
    }

    public function listAllBranches()
    {
        return Branch::with(['city.country'])->get();
    }

    public function createBranch(array $data)
    {
        return Branch::create($data);
    }

    public function listAllEmployees()
    {
        return Employee::with(['user', 'branch'])->get();
    }

    public function assignEmployeeToBranch($userId, $branchId)
    {
        $user = User::findOrFail($userId);
        
        // Ensure user has employee role
        if (!$user->hasRole(RoleName::EMPLOYEE->value)) {
            $user->assignRole(RoleName::EMPLOYEE->value);
        }

        return Employee::updateOrCreate(
            ['user_id' => $userId],
            ['branch_id' => $branchId, 'status' => 'active']
        );
    }

    public function getGlobalParcels()
    {
        return Parcel::with(['sender', 'receiver', 'branch'])->latest()->paginate(15);
    }
}
