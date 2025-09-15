<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrequentlyAskedQuestions extends Model
{

    protected $fillable = [
        'question',
        'answer',
        'category_type',
        'is_show',
    ];
}
