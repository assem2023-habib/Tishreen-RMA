<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الأدوار
        $adminRole = Role::firstOrCreate(
            ['role_name' => 'Admin'],
            ['description' => 'مدير النظام']
        );

        $employeeRole = Role::firstOrCreate(
            ['role_name' => 'Employee'],
            ['description' => 'موظف في النظام']
        );

        // إنشاء الصلاحيات
        $permissions = [
            ['permission_name' => 'access_admin_panel', 'description' => 'يستطيع الدخول الى الادمن بانل'],
            ['permission_name' => 'assign_employee', 'description' => 'تعيين موظف'],
            ['permission_name' => 'remove_employee', 'description' => 'ازالة موظف'],
            ['permission_name' => 'promote_employee_to_admin', 'description' => 'ترقية موظف الى مدير'],
            ['permission_name' => 'demote_employee_from_admin', 'description' => 'ازلة موظف من منصب مدير'],
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate(
                ['permission_name' => $perm['permission_name']],
                ['description' => $perm['description']]
            );

            // ربط جميع الصلاحيات بدور المدير فقط
            $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}
