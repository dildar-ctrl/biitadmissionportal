@extends('admin.layout')

@section('title', 'View Profile')

@push('styles')
<style>
    .view-section {
        background-color: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .view-section h6 {
        color: var(--primary);
        font-weight: 800;
        font-size: 1rem;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
    }

    .info-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .info-item {
        width: 33.33%;
        padding-right: 12px;
        margin-bottom: 10px;
    }

    .info-item label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: var(--text-muted);
        margin-bottom: 3px;
    }

    .info-item span {
        font-size: 0.9rem;
        color: var(--text-dark);
        font-weight: 600;
    }

    @media (max-width: 700px) {
        .info-item { width: 50%; }
    }

    .edu-card, .doc-card {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px 18px;
        margin-bottom: 14px;
    }

    .edu-card:last-child, .doc-card:last-child {
        margin-bottom: 0;
    }

    .edu-card .edu-title {
        font-weight: 800;
        color: var(--primary);
        font-size: 0.88rem;
        margin-bottom: 10px;
    }

    .doc-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .doc-card .doc-type {
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--text-dark);
    }

    .doc-card a {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--primary);
        text-decoration: none;
    }

    .doc-card a:hover {
        text-decoration: underline;
    }

    .edit-btn {
        display: inline-block;
        background-color: var(--primary);
        color: #fff;
        border: none;
        padding: 11px 28px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .edit-btn:hover {
        background-color: var(--primary-dark);
        color: #fff;
    }

    .empty-note {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    /* Buttons row — Cancel + Edit Profile, aligned to the right */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 8px;
    }

    .cancel-btn {
        background-color: #fff;
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        padding: 11px 28px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .cancel-btn:hover {
        background-color: var(--bg-soft);
        color: var(--text-dark);
    }
</style>
@endpush

@section('content')

<div class="stepper-title">View Profile</div>

@if(!$candidate)

    <div class="view-section">
        <p class="empty-note">You haven't completed your profile yet.</p>
        <a href="{{ route('profile.edit') }}" class="edit-btn">Complete Profile</a>
    </div>

@else

    <div class="view-section">
        <h6>Personal Information</h6>
        <div class="info-row">
            <div class="info-item"><label>First Name</label><span>{{ $candidate->fname }}</span></div>
            <div class="info-item"><label>Last Name</label><span>{{ $candidate->lname }}</span></div>
            <div class="info-item"><label>CNIC</label><span>{{ $candidate->cnic }}</span></div>

            <div class="info-item"><label>Date of Birth</label><span>{{ $candidate->dob }}</span></div>
            <div class="info-item"><label>Gender</label><span>{{ $candidate->gender }}</span></div>
            <div class="info-item"><label>Marital Status</label><span>{{ $candidate->marital_status }}</span></div>

            <div class="info-item"><label>Mobile Number</label><span>{{ $candidate->mobile_no }}</span></div>
            <div class="info-item"><label>Alternative Mobile</label><span>{{ $candidate->alternative_mobile_no ?: '—' }}</span></div>
            <div class="info-item"><label>Religion</label><span>{{ $candidate->religion }}</span></div>

            <div class="info-item" style="width: 66.66%;"><label>Postal Address</label><span>{{ $candidate->postal_address }}</span></div>
            <div class="info-item" style="width: 66.66%;"><label>Permanent Address</label><span>{{ $candidate->permanent_address }}</span></div>

            <div class="info-item"><label>District</label><span>{{ $candidate->district }}</span></div>
            <div class="info-item"><label>Province of Domicile</label><span>{{ $candidate->province_of_domicile }}</span></div>
            <div class="info-item"><label>District of Domicile</label><span>{{ $candidate->district_of_domicile }}</span></div>

            <div class="info-item"><label>Email</label><span>{{ $candidate->email }}</span></div>
        </div>
    </div>

    <div class="view-section">
        <h6>Father's Details</h6>
        <div class="info-row">
            <div class="info-item"><label>Father's Name</label><span>{{ $candidate->father_name }}</span></div>
            <div class="info-item"><label>Father's CNIC</label><span>{{ $candidate->father_cnic }}</span></div>
            <div class="info-item"><label>Father's Occupation</label><span>{{ $candidate->father_occupation }}</span></div>

            <div class="info-item"><label>Father's Mobile</label><span>{{ $candidate->father_mobile_no }}</span></div>
            <div class="info-item"><label>Father's Email</label><span>{{ $candidate->father_email }}</span></div>
        </div>
    </div>

    <div class="view-section">
        <h6>Educational Qualification</h6>

        @forelse($candidate->education as $i => $edu)
        <div class="edu-card">
            <div class="edu-title">Degree #{{ $i + 1 }} — {{ $edu->degree }}</div>
            <div class="info-row">
                <div class="info-item"><label>Major Subjects</label><span>{{ $edu->majorSubjects->pluck('title')->join(', ') ?: '—' }}</span></div>
                <div class="info-item"><label>Board / University</label><span>{{ $edu->boardUniversity->title ?? '—' }}</span></div>

                <div class="info-item"><label>Date of Passing</label><span>{{ $edu->date_of_passing }}</span></div>
                <div class="info-item"><label>Marks Type</label><span>{{ $edu->marksType->marks_type ?? '—' }}</span></div>
                <div class="info-item"><label>Marks</label><span>{{ $edu->obtained_marks }} / {{ $edu->total_marks }}</span></div>
            </div>
        </div>
        @empty
        <p class="empty-note">No education records added yet.</p>
        @endforelse
    </div>

    <div class="view-section">
        <h6>Uploaded Documents</h6>

        @forelse($candidate->documents as $doc)
        <div class="doc-card">
            <span class="doc-type">{{ $doc->documentType->type ?? 'Document' }}</span>
            <a href="{{ route('document.file', $doc->id) }}" target="_blank">View File</a>
        </div>
        @empty
        <p class="empty-note">No documents uploaded yet.</p>
        @endforelse
    </div>

    {{-- Cancel goes back to the dashboard; Edit Profile goes to the profile form; Continue moves on to Apply & Submit --}}
    <div class="form-actions">
        <a href="{{ route('dashboard') }}" class="cancel-btn">Cancel</a>
        <a href="{{ route('profile.edit') }}" class="edit-btn"><i class="fas fa-edit me-2"></i>Edit Profile</a>
        <a href="{{ route('apply.index') }}" class="edit-btn"><i class="fas fa-arrow-right me-2"></i>Continue to Apply</a>
    </div>

@endif

@endsection