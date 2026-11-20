<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'session'; // singular
    protected $fillable = ['name', 'active', 'start_date', 'session_end_date', 'semester_end_date'];
}