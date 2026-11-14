<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationChoice extends Model
{
    protected $table = 'applicationchoices';

    public $timestamps = true;

    protected $fillable = [
        'application_id',
        'program_id',
        'priority',
        'admin_status',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Application jis mein ye choice hai
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    /**
     * Program ki details
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    // ========== SCOPES ==========

    public function scopeApproved($query)
    {
        return $query->where('admin_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('admin_status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('admin_status', 'rejected');
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority');
    }

    // ========== METHODS ==========

    /**
     * Get program name with priority
     */
    public function getProgramNameWithPriority()
    {
        return "Priority {$this->priority}: {$this->program->name}";
    }

    /**
     * Get status badge (for frontend)
     */
    public function getStatusBadge()
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'approved' => '<span class="badge badge-success">Approved ✓</span>',
            'rejected' => '<span class="badge badge-danger">Rejected ✗</span>',
        ];

        return $badges[$this->admin_status] ?? 'Unknown';
    }

    /**
     * Check if this is first priority choice
     */
    public function isFirstPriority(): bool
    {
        return $this->priority === 1;
    }

    /**
     * Check if approved
     */
    public function isApproved(): bool
    {
        return $this->admin_status === 'approved';
    }

    /**
     * Check if rejected
     */
    public function isRejected(): bool
    {
        return $this->admin_status === 'rejected';
    }

    /**
     * Check if pending
     */
    public function isPending(): bool
    {
        return $this->admin_status === 'pending';
    }
}