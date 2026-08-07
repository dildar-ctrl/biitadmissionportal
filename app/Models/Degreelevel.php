<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DegreeLevel extends Model
{
    protected $table = 'degreelevel';
    public $timestamps = false;

    protected $fillable = ['title'];

    public function categories()
    {
        return $this->hasMany(Category::class, 'degreelevel_id');
    }

    public function programs()
    {
        return $this->hasMany(Program::class, 'degreelevel_id');
    }
}