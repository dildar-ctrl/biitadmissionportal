<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $table = 'candidate';

    protected $fillable = [
        'candidate_login_id',
        'fname',
        'lname',
        'father_name',
        'father_cnic',
        'father_occupation',
        'father_mobile_no',
        'father_email',
        'cnic',
        'email',
        'dob',
        'postal_address',
        'permanent_address',
        'district',
        'gender',
        'mobile_no',
        'alternative_mobile_no',
        'religion',
        'marital_status',
        'province_of_domicile',
        'district_of_domicile',
    ];

    public function login()
    {
        return $this->belongsTo(CandidateLogin::class, 'candidate_login_id');
    }

    public function education()
    {
        return $this->hasMany(Education::class, 'candidate_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'candidate_id');
    }
}