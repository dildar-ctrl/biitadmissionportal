@extends('layouts.app')

@section('title', 'Dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">
@endpush
@section('styles')
<style>
    .dashboard-header {
        margin-bottom: 40px;
    }

    .greeting-text {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .greeting-subtext {
        font-size: 0.95rem;
        color: var(--text-muted);
    }

    /* -------- APPLICATION STATUS CARD -------- */
  .app-status-card {
    background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
    border-radius: 16px;
    padding: 16px 24px;
    color: #fff;
    margin-bottom: 32px;
    box-shadow: 0 8px 18px rgba(23,117,59,0.28);
}

.app-status-label {
    font-size: 0.7rem;
    letter-spacing: 0.08em;
    font-weight: 700;
    opacity: 0.85;
    margin-bottom: 4px;
    text-transform: uppercase;
}

.app-status-number {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.app-status-badge {
    background-color: rgba(255, 255, 255, 0.25);
    color: #fff;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
}

.progress-section {
    margin-top: 4px;
}

.progress-label {
    font-size: 0.75rem;
    margin-bottom: 5px;
    display: flex;
    justify-content: space-between;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    overflow: hidden;
}
    .progress-fill {
        height: 100%;
        background-color: #fff;
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    /* -------- STATUS CARDS GRID -------- */
    .status-cards-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }

    .status-card {
        background-color: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 28px;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
    }

    .status-card:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 16px rgba(12, 115, 71, 0.12);
    }

    .status-card-icon {
        width: 56px;
        height: 56px;
      background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #fff;
        margin-bottom: 16px;
    }

    .status-card-icon.disabled {
        background-color: #d1d5db;
    }

    .status-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-indicator {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid var(--primary);
        display: inline-flex;
    }

    .status-indicator.completed {
      background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
    }

    .status-card-description {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 16px;
        flex-grow: 1;
    }

    .status-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid var(--border);
    }

    .status-card-footer span {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .status-card-arrow {
        color: var(--primary);
        font-size: 1.2rem;
    }

    /* -------- NEXT STEP SECTION -------- */
    .next-step-section {
        background-color: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 28px;
        text-align: center;
    }

    .next-step-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 12px;
    }

    .next-step-description {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 20px;
    }

    .continue-btn {
       background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .continue-btn:hover {
        background-color: var(--primary-light);
        transform: translateX(4px);
    }

    @media (max-width: 768px) {
        .status-cards-grid {
            grid-template-columns: 1fr;
        }

        .greeting-text {
            font-size: 1.4rem;
        }
    }
</style>
@endsection

@section('content')

<!-- APPLICATION STATUS CARD -->
<div class="app-status-card">

    <div class="app-status-number">
        <div>
            <div class="app-status-label">Application Number</div>
            {{ $applicationNumber ?? 'Not generated yet' }}
        </div>
        <span class="app-status-badge">{{ $applicationStatus }}</span>
    </div>

    <div class="progress-section">
        <div class="progress-label">
            <span>Completion</span>
            <span>{{ $completionPercent }}%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $completionPercent }}%;"></div>
        </div>
    </div>
</div>

<!-- STATUS CARDS GRID -->
<div class="status-cards-grid">
    <!-- Program Selected -->
    <a href="{{ route('apply.index') }}" class="status-card">
        <div class="status-card-icon {{ $programSelected ? '' : 'disabled' }}">👨‍🎓</div>
        <div class="status-card-title">
            <span class="status-indicator {{ $programSelected ? 'completed' : '' }}"></span>
            Program selected
        </div>
        <div class="status-card-description">
            @if($programSelected)
                {{ $selectedProgramName }} ({{ $programChoicesCount }} {{ $programChoicesCount == 1 ? 'choice' : 'choices' }} selected)
            @else
                Not selected yet
            @endif
        </div>
        <div class="status-card-footer">
            <span></span>
            <span class="status-card-arrow">→</span>
        </div>
    </a>

    <!-- Profile Completed -->
    <a href="{{ route('profile.edit') }}" class="status-card">
        <div class="status-card-icon {{ $profileComplete ? '' : 'disabled' }}">👤</div>
        <div class="status-card-title">
            <span class="status-indicator {{ $profileComplete ? 'completed' : '' }}"></span>
            Profile completed
        </div>
        <div class="status-card-description">{{ $profileFilledCount }}/{{ $profileTotalFields }} fields filled</div>
        <div class="status-card-footer">
            <span></span>
            <span class="status-card-arrow">→</span>
        </div>
    </a>

    <!-- Documents Uploaded -->
    <a href="{{ route('document.edit') }}" class="status-card">
        <div class="status-card-icon {{ $documentsComplete ? '' : 'disabled' }}">📁</div>
        <div class="status-card-title">
            <span class="status-indicator {{ $documentsComplete ? 'completed' : '' }}"></span>
            Documents uploaded
        </div>
        <div class="status-card-description">{{ $documentsUploadedCount }}/{{ $documentsTotalTypes }} documents uploaded</div>
        <div class="status-card-footer">
            <span></span>
            <span class="status-card-arrow">→</span>
        </div>
    </a>

    <!-- Application Submitted -->
    <a href="{{ route('apply.status') }}" class="status-card">
        <div class="status-card-icon {{ $applicationSubmitted ? '' : 'disabled' }}">📋</div>
        <div class="status-card-title">
            <span class="status-indicator {{ $applicationSubmitted ? 'completed' : '' }}"></span>
            Application submitted
        </div>
        <div class="status-card-description">{{ $applicationSubmitted ? 'Submitted' : 'Review and submit your application' }}</div>
        <div class="status-card-footer">
            <span></span>
            <span class="status-card-arrow">→</span>
        </div>
    </a>
</div>

<!-- NEXT STEP SECTION -->
<div class="next-step-section">
    <div class="next-step-title">Next step</div>
    <div class="next-step-description">{{ $nextStepText }}</div>
    <a href="{{ $nextStepRoute }}" class="continue-btn">
        Continue
        <i class="fas fa-arrow-right"></i>
    </a>
</div>

@endsection