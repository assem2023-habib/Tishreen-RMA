<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Trait\BlamableTrait;

class Country extends Model
{
    use BlamableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'en_name',
        'ar_name',
        'code',
        'domain_code',
        'image',

        'created_by',
        'updated_by',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, City::class);
    }

    public static function getCountriesByLang(string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale === 'ar') {
            return self::pluck('id', 'ar_name')->toArray();
        }

        return self::pluck('id', 'en_name')->toArray();
    }
}
