<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationRatingScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'label',
        'description',
        'sort_order',
    ];
}
