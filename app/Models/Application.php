<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $table = 'applications'; // Changed from 'application' to 'applications'

    public $timestamps = true; // Changed to true (now using created_at, updated_at)

    protected $fillable = [
        'candidate_id',
        'application_no',
        'status',
        'applied_at',
        'undertaking_admission_accepted',
        'undertaking_refund_accepted',
        'undertaking_conduct_accepted',
        'undertakings_completed_at',
    ];

    protected $casts = [
        'undertaking_admission_accepted' => 'boolean',
        'undertaking_refund_accepted' => 'boolean',
        'undertaking_conduct_accepted' => 'boolean',
        'applied_at' => 'datetime',
        'undertakings_completed_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    /**
     * 3 programs ke liye 3 choices
     */
    public function choices(): HasMany
    {
        return $this->hasMany(ApplicationChoice::class, 'application_id')->orderBy('priority');
    }

    // ========== SCOPES ==========

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ========== ATTRIBUTES & METHODS ==========

    /**
     * Check if all undertakings accepted
     */
    public function getUndertakingsCompleteAttribute(): bool
    {
        return $this->undertaking_admission_accepted
            && $this->undertaking_refund_accepted
            && $this->undertaking_conduct_accepted;
    }

    /**
     * Get approved program choices
     */
    public function getApprovedChoices()
    {
        return $this->choices()
            ->where('admin_status', 'approved')
            ->with('program')
            ->orderBy('priority')
            ->get();
    }

    /**
     * Get all choices with program details
     */
    public function getProgramsWithStatus()
    {
        return $this->choices()
            ->with('program')
            ->orderBy('priority')
            ->get();
    }
}