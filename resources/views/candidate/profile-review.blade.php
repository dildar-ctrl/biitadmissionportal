@extends('layouts.app')

@section('title', 'Profile Review')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">

<style>
    .review-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 24px;
        margin-bottom: 20px;
    }

    .review-card h5 {
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 18px;
        color: var(--text-dark);
        border-bottom: 2px solid var(--primary);
        padding-bottom: 12px;
    }

    .review-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 16px;
    }

    .review-row.full {
        grid-template-columns: 1fr;
    }

    .review-field {
        display: flex;
        flex-direction: column;
    }

    .review-field label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
    }

    .review-field .value {
        font-size: 0.95rem;
        color: var(--text-dark);
        font-weight: 500;
    }

    .programs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        margin-top: 16px;
    }

    .program-badge {
   background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
    color: #fff;
    padding: 16px;
    border-radius: 10px;
    text-align: center;
}

    .program-badge .priority {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.85;
        display: block;
        margin-bottom: 6px;
    }

    .program-badge .name {
        font-size: 0.95rem;
        font-weight: 700;
    }

    .documents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 16px;
    }

    .document-item {
        background: #f8f9fa;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px;
        text-align: center;
    }

    .document-item .icon {
        font-size: 1.8rem;
        margin-bottom: 6px;
    }

    .document-item .name {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-dark);
        word-break: break-word;
    }

    .document-item.uploaded {
        background: rgba(34, 197, 94, 0.08);
        border-color: #22c55e;
    }

    .document-item.uploaded .icon {
        color: #22c55e;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-primary-custom {
       background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: opacity 0.2s ease;
        flex: 1;
    }

    .btn-primary-custom:hover {
        opacity: 0.9;
    }

    .btn-secondary-custom {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-secondary-custom:hover {
        background: var(--primary);
        color: #fff;
    }

    .education-block {
        background: #f8f9fa;
        border-left: 4px solid var(--primary);
        padding: 16px;
        border-radius: 6px;
        margin-bottom: 12px;
    }

    .education-block .degree {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .education-block .info {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.6;
    }
</style>
@endpush

@section('content')

<div class="stepper-title">Review Your Profile</div>

<!-- PERSONAL INFORMATION -->
<div class="review-card">
    <h5><i class="fas fa-user-circle"></i> Personal Information</h5>

    <div class="review-row">
        <div class="review-field">
            <label>Full Name</label>
            <div class="value">{{ strtoupper(($candidate->fname ?? '') . ' ' . ($candidate->lname ?? '')) }}</div>
        </div>
        <div class="review-field">
            <label>CNIC</label>
            <div class="value">{{ $candidate->cnic ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="review-row">
        <div class="review-field">
            <label>Date of Birth</label>
            <div class="value">{{ $candidate->dob ? \Carbon\Carbon::parse($candidate->dob)->format('d-M-Y') : 'N/A' }}</div>
        </div>
        <div class="review-field">
            <label>Gender</label>
            <div class="value">{{ $candidate->gender ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="review-row">
        <div class="review-field">
            <label>Email</label>
            <div class="value">{{ $candidate->email ?? 'N/A' }}</div>
        </div>
        <div class="review-field">
            <label>Mobile No</label>
            <div class="value">{{ $candidate->mobile_no ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="review-row">
        <div class="review-field">
            <label>Religion</label>
            <div class="value">{{ $candidate->religion ?? 'N/A' }}</div>
        </div>
        <div class="review-field">
            <label>Nationality</label>
            <div class="value">{{ $candidate->nationality ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="review-row">
        <div class="review-field">
            <label>Marital Status</label>
            <div class="value">{{ $candidate->marital_status ?? 'N/A' }}</div>
        </div>
        <div class="review-field">
            <label>Medical Issue</label>
            <div class="value">{{ $candidate->medicalissue ?? 'None' }}</div>
        </div>
    </div>

    <div class="review-row full">
        <div class="review-field">
            <label>Postal Address</label>
            <div class="value">{{ $candidate->postal_address ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="review-row full">
        <div class="review-field">
            <label>Permanent Address</label>
            <div class="value">{{ $candidate->permanent_address ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="review-row">
        <div class="review-field">
            <label>Sports & Co-curricular Activities</label>
            <div class="value">{{ $candidate->activities ?? 'None' }}</div>
        </div>
    </div>
</div>

<!-- FATHER/GUARDIAN INFORMATION -->
<div class="review-card">
    <h5><i class="fas fa-shield-alt"></i> Father/Guardian Information</h5>

    <div class="review-row">
        <div class="review-field">
            <label>Name</label>
            <div class="value">{{ $candidate->father_name ?? 'N/A' }}</div>
        </div>
        <div class="review-field">
            <label>CNIC</label>
            <div class="value">{{ $candidate->father_cnic ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="review-row">
        <div class="review-field">
            <label>Contact No</label>
            <div class="value">{{ $candidate->father_mobile_no ?? 'N/A' }}</div>
        </div>
        <div class="review-field">
            <label>Relationship</label>
            <div class="value">{{ $candidate->relationshipwithguardian ?? 'Father' }}</div>
        </div>
    </div>
</div>

<!-- EMERGENCY CONTACT -->
<div class="review-card">
    <h5><i class="fas fa-phone-alt"></i> Emergency Contact</h5>

    <div class="review-row">
        <div class="review-field">
            <label>Name</label>
            <div class="value">{{ $candidate->emergencycontactname ?? 'N/A' }}</div>
        </div>
        <div class="review-field">
            <label>Relationship</label>
            <div class="value">{{ $candidate->emergencycontactrelationship ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="review-row">
        <div class="review-field">
            <label>Contact No</label>
            <div class="value">{{ $candidate->emergencycontact ?? 'N/A' }}</div>
        </div>
    </div>
</div>

<!-- ACADEMIC RECORD -->
@if($education->isNotEmpty())
<div class="review-card">
    <h5><i class="fas fa-graduation-cap"></i> Academic Record</h5>

    @foreach($education as $edu)
        <div class="education-block">
            <div class="degree">{{ $edu->degree ?? 'N/A' }}</div>
            <div class="info">
                <strong>Board/University:</strong> {{ $edu->boardname ?? 'N/A' }}<br>
                <strong>Major Subjects:</strong> {{ $edu->majorsubjects ?? 'N/A' }}<br>
                <strong>Marks:</strong> {{ $edu->obtained_marks ?? 'N/A' }} / {{ $edu->total_marks ?? 'N/A' }}<br>
                <strong>Division/Grade:</strong> {{ $edu->division ?? 'N/A' }}<br>
                <strong>Percentage:</strong> 
                @if($edu->percentage)
                    {{ $edu->percentage }}%
                @elseif($edu->total_marks > 0)
                    {{ number_format(($edu->obtained_marks / $edu->total_marks) * 100, 1) }}%
                @else
                    N/A
                @endif
                <br>
                <strong>Passing Year:</strong> {{ $edu->date_of_passing ? \Carbon\Carbon::parse($edu->date_of_passing)->format('Y') : 'N/A' }}
            </div>
        </div>
    @endforeach
</div>
@endif
<!-- DOCUMENTS -->
@if($documents->isNotEmpty())
<div class="review-card">
    <h5><i class="fas fa-file-upload"></i> Uploaded Documents</h5>
    
    <div class="documents-grid">
        @foreach($documents as $doc)
            <a href="{{ route('document.file', $doc->id) }}" target="_blank" class="document-item uploaded" style="text-decoration: none; cursor: pointer;">
                <div class="icon"><i class="fas fa-file-pdf"></i></div>
                <div class="name">{{ $doc->documentType->type ?? 'Document' }}</div>
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- PROGRAMS -->
<div class="review-card">
    <h5><i class="fas fa-list"></i> Selected Programs</h5>

    <div class="programs-grid">
        @foreach($programs as $program)
            <div class="program-badge">
                <span class="priority">
                    {{ $program['priority'] == 1 ? '1st Choice' : ($program['priority'] == 2 ? '2nd Choice' : '3rd Choice') }}
                </span>
                <div class="name">{{ $program['name'] }}</div>
            </div>
        @endforeach
    </div>
</div>

<!-- ACTION BUTTONS -->
@if(!$isSubmitted)
<div class="review-card">
    <div class="button-group">
        <form method="GET" action="{{ route('undertakings.show') }}" style="flex: 1;">
            <button type="submit" class="btn-primary-custom" style="width: 100%;">
                Proceed to Undertakings <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>
@endif
@endsection