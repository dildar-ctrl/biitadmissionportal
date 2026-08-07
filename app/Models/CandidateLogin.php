<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CandidateLogin extends Authenticatable
{
    use Notifiable;

    protected $table = 'candidatelogin';

    protected $fillable = [
        'cnic',
        'fname',
        'lname',
        'mobile_no',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    // relationship to detailed profile
    public function candidate()
    {
        return $this->hasOne(Candidate::class, 'candidate_login_id');
    }
}