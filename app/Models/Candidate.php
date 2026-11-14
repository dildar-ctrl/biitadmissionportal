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
        'relationshipwithguardian',
        'father_mobile_no',
        'emergencycontactname',
        'cnic',
        'email',
        'dob',
        'postal_address',
        'permanent_address',
        'emergencycontactrelationship',
        'gender',
        'mobile_no',
        'emergencycontact',
        'religion',
        'marital_status',
        'nationality',
        'medicalissue',
        'activities',
        'PMASREGNO',
    ];

    protected $attributes = [
        'fname' => null,
        'lname' => null,
    ];

    // ========== RELATIONSHIPS ==========

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

    /**
     * All applications submitted by this candidate
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'candidate_id')->latest();
    }

    /**
     * Get latest/current application
     */
    public function latestApplication()
    {
        return $this->hasOne(Application::class, 'candidate_id')->latest();
    }

    // ========== HELPER METHODS ==========

    /**
     * Get latest application with all program choices
     */
    public function getLatestApplicationWithChoices()
    {
        return Application::where('candidate_id', $this->id)
            ->with('choices.program')
            ->latest()
            ->first();
    }

    /**
     * Get all program choices from latest application
     */
    public function getLatestApplicationChoices()
    {
        $application = $this->getLatestApplicationWithChoices();
        return $application ? $application->choices : collect();
    }

    /**
     * Get application number
     */
    public function getApplicationNumber()
    {
        $application = $this->getLatestApplicationWithChoices();
        return $application ? $application->application_no : null;
    }

    /**
     * Check if candidate has submitted application
     */
    public function hasApplication()
    {
        return Application::where('candidate_id', $this->id)->exists();
    }

    /**
     * Check if application is pending
     */
    public function hasApplicationPending()
    {
        return Application::where('candidate_id', $this->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Check if all undertakings accepted
     */
    public function hasAcceptedUndertakings()
    {
        $application = $this->getLatestApplicationWithChoices();
        return $application ? $application->undertakings_complete : false;
    }

    /**
     * Get approved programs for candidate
     */
    public function getApprovedPrograms()
    {
        $application = $this->getLatestApplicationWithChoices();
        if (!$application) {
            return collect();
        }

        return $application->choices()
            ->where('admin_status', 'approved')
            ->with('program')
            ->orderBy('priority')
            ->get();
    }

    /**
     * Get full name
     */
    public function getFullName()
    {
        return trim($this->fname . ' ' . $this->lname);
    }
    public function isProfileComplete(): bool
{
    // ---- 1. Profile fields ----
    $requiredFields = [
        'father_name', 'father_cnic', 'relationshipwithguardian', 'father_mobile_no',
        'postal_address', 'permanent_address', 'gender', 'religion',
        'marital_status', 'nationality', 'medicalissue',
        'emergencycontactname', 'emergencycontactrelationship', 'emergencycontact',
    ];

    foreach ($requiredFields as $field) {
        if (empty($this->$field)) {
            return false;
        }
    }

    // ---- 2. Education: needs both Matric AND Intermediate ----
    $degrees = $this->education()->pluck('degree')->map(fn($d) => strtolower($d));

    $hasMatric = $degrees->contains(fn($d) =>
        str_contains($d, 'matric') || str_contains($d, 'o level') || str_contains($d, 'o-level')
    );

    $hasIntermediate = $degrees->contains(fn($d) =>
        str_contains($d, 'intermediate') || str_contains($d, 'f.sc') || str_contains($d, 'fsc')
        || str_contains($d, 'a level') || str_contains($d, 'a-level') || str_contains($d, 'hssc')
    );

    if (!$hasMatric || !$hasIntermediate) {
        return false;
    }

    // ---- 3. Documents: every document type in the system must be uploaded ----
    $allDocumentTypeIds = \App\Models\DocumentType::pluck('id');

    if ($allDocumentTypeIds->isNotEmpty()) {
        $uploadedTypeIds = $this->documents()->pluck('documenttype_id');
        $missing = $allDocumentTypeIds->diff($uploadedTypeIds);

        if ($missing->isNotEmpty()) {
            return false;
        }
    }

    return true;
}
}