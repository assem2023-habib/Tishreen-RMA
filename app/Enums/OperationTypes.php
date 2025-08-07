<?php

namespace App\Enums;

enum OperationTypes: string
{
    case CREATED = "Created";
    case INSERTED = "Inserted";
    case UPDATED = "Updated";
    case DELETED = "Deleted";
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
