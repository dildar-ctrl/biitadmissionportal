<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    // education table only has created_at (no updated_at), so timestamps stay off
    public $timestamps = false;

    protected $table = 'education';

    protected $fillable = [
        'candidate_id',
        'degree',
        'boardname',
        'majorsubjects',
        'date_of_passing',
        'registrationno',
        'obtained_marks',
        'total_marks',
        'percentage',
        'division',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }
}