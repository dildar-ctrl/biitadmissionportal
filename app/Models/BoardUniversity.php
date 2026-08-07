<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardUniversity extends Model
{
    protected $table = 'boarduniversity';
    public $timestamps = false;
    protected $fillable = ['title'];
}