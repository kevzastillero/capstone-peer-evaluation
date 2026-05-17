<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicBlock extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function groups()
    {
        return $this->hasMany(ProjectGroup::class);
    }

    public function students()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }
}
