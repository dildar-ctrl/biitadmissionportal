<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'program';

    protected $fillable = ['name', 'session_id', 'status', 'degreelevel_id'];

    public function degreeLevel()
    {
        return $this->belongsTo(DegreeLevel::class, 'degreelevel_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function pattern()
    {
        return $this->hasMany(ProgramPattern::class, 'program_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'program_id');
    }
}