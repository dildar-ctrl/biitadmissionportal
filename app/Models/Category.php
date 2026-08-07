<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    public $timestamps = false;

    protected $fillable = ['degreelevel_id', 'title'];

    public function degreeLevel()
    {
        return $this->belongsTo(DegreeLevel::class, 'degreelevel_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'category_id');
    }
}