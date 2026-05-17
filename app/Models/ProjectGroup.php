<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectGroup extends Model
{
    use HasFactory;

    protected $fillable = ['academic_block_id', 'number'];

    public function block()
    {
        return $this->belongsTo(AcademicBlock::class, 'academic_block_id');
    }

    public function students()
    {
        return $this->hasMany(User::class)->where('role', 'student')->orderBy('name');
    }
}
