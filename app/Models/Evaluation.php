<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluator_id',
        'evaluatee_id',
        'participation_score',
        'quality_score',
        'collaboration_score',
        'communication_score',
        'reliability_score',
        'comments',
    ];

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluatee()
    {
        return $this->belongsTo(User::class, 'evaluatee_id');
    }

    public function answers()
    {
        return $this->hasMany(EvaluationAnswer::class);
    }

    public function getAverageScoreAttribute()
    {
        if ($this->relationLoaded('answers') && $this->answers->count() > 0) {
            return round($this->answers->avg('score'), 2);
        }

        return round(collect([
            $this->participation_score,
            $this->quality_score,
            $this->collaboration_score,
            $this->communication_score,
            $this->reliability_score,
        ])->average(), 2);
    }
}
