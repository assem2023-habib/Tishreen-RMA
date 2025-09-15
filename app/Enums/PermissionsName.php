<?php

namespace App\Enums;

enum PermissionsName: string
{
    //
    //
    //
    //
    //

    case ACCESS_ADMIN_PANEL = 'access_admin_panel';
    case ASSIGN_EMPLOYEE = 'assign_employee';
    case REMOVE_EMPLOYEE = 'remove_employee';
    case PROMOTE_EMPLOYEE_TO_ADMIN = 'promote_employee_to_admin';
    case DEMOTE_EMPLOYEE_FROM_ADMIN = 'demote_employee_from_admin';
}
