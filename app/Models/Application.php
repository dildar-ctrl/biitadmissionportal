<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'application';
    public $timestamps = false;

    protected $fillable = [
        'candidate_id',
        'program_id',
        'application_no',
        'status',
        'applied_at',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function rollSlip()
    {
        return $this->hasOne(RollSlip::class, 'application_id');
    }
}