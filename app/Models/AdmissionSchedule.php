<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionSchedule extends Model
{
    protected $table = 'admissionschedule';

    const CREATED_AT = 'createdat';
    const UPDATED_AT = 'updatedat';

    protected $fillable = ['sessionid', 'admissionopendate', 'admissionclosedate'];

    protected $casts = [
        'admissionopendate' => 'date',
        'admissionclosedate' => 'date',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class, 'sessionid');
    }

    /**
     * The one schedule (if any) that's currently open today.
     */
    public static function current()
    {
        return static::whereDate('admissionopendate', '<=', now())
            ->whereDate('admissionclosedate', '>=', now())
            ->first();
    }
}