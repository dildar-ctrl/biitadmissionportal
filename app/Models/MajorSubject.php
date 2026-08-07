<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MajorSubject extends Model
{
    protected $table = 'majorsubjects';
    public $timestamps = false;
    protected $fillable = ['title'];
}