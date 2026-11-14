<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramFeeStructure extends Model
{
    protected $table = 'program_fee_structure';

    // Composite primary key - Eloquent doesn't natively support this,
    // so we disable auto-incrementing and the single-key assumption.
    public $incrementing = false;
    protected $primaryKey = null; // composite keys aren't directly supported by Eloquent

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'semester',
        'program_id',
        'challan_param_id',
        'amount',
        'admission_fee',
    ];

    protected $casts = [
        'session_id' => 'integer',
        'semester' => 'integer',
        'program_id' => 'integer',
        'challan_param_id' => 'integer',
        'amount' => 'decimal:2',
        'admission_fee' => 'integer',
    ];

    // ========== RELATIONSHIPS ==========

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

}