@extends('admin.layout')

@section('title', 'Overview')

@push('styles')
<style>
    /* status-row is now also used as a clickable <a> — keep its look
       identical to the plain div version, just add a hover cue */
    a.status-row {
        color: inherit;
        display: flex;
        border-radius: 8px;
        margin: 0 -8px;
        padding: 12px 8px;
    }

    a.status-row:hover {
        background-color: var(--bg-soft);
    }
</style>
@endpush

@section('content')

<div class="section-heading">
    <h4>Overview</h4>
    <select class="pill-select">
        <option>All Sessions</option>
    </select>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="icon-box" style="background-color: #ece9fb; color: #3e4095;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="value">{{ $totalCandidates ?? 0 }}</div>
                <div class="label">Total Candidates</div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="icon-box" style="background-color: #fdeee0; color: #f59e0b;">
                <i class="fas fa-file-alt"></i>
            </div>
            <div>
                <div class="value">{{ $totalTests ?? 0 }}</div>
                <div class="label">Total Tests</div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="icon-box" style="background-color: #e1f8f0; color: #22c55e;">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <div class="value">{{ $totalAttempts ?? 0 }}</div>
                <div class="label">Total Test Attempts</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card-panel">
            <h6>Test Attempt Status</h6>

            <div class="status-row">
                <span><span class="dot" style="background-color:#22c55e;"></span>Completed (Pass)</span>
                <span class="right">
                    <strong>{{ $passCount ?? 0 }}</strong>
                    <span class="pct-pill">{{ $passPct ?? 0 }}%</span>
                </span>
            </div>

            <div class="status-row">
                <span><span class="dot" style="background-color:#ef4444;"></span>Failed</span>
                <span class="right">
                    <strong>{{ $failCount ?? 0 }}</strong>
                    <span class="pct-pill">{{ $failPct ?? 0 }}%</span>
                </span>
            </div>

            <div class="status-row">
                <span><span class="dot" style="background-color:#f59e0b;"></span>Pending</span>
                <span class="right">
                    <strong>{{ $pendingCount ?? 0 }}</strong>
                    <span class="pct-pill">{{ $pendingPct ?? 0 }}%</span>
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-panel">
            <h6>Application Status</h6>

            {{-- Each row links to the admin applications list, pre-filtered by that status --}}
            <a href="{{ route('admin.applications.index') }}" class="status-row" style="text-decoration:none; cursor:pointer;">
                <span><span class="dot" style="background-color:#3e4095;"></span>Total Applications</span>
                <span class="right"><strong>{{ $totalApplications ?? 0 }}</strong></span>
            </a>

            <a href="{{ route('admin.applications.index', ['status' => 'approved']) }}" class="status-row" style="text-decoration:none; cursor:pointer;">
                <span><span class="dot" style="background-color:#22c55e;"></span>Approved</span>
                <span class="right"><strong>{{ $approvedApplications ?? 0 }}</strong></span>
            </a>

            <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}" class="status-row" style="text-decoration:none; cursor:pointer;">
                <span><span class="dot" style="background-color:#f59e0b;"></span>Pending</span>
                <span class="right"><strong>{{ $pendingApplications ?? 0 }}</strong></span>
            </a>

            <a href="{{ route('admin.applications.index', ['status' => 'rejected']) }}" class="status-row" style="text-decoration:none; cursor:pointer;">
                <span><span class="dot" style="background-color:#ef4444;"></span>Rejected</span>
                <span class="right"><strong>{{ $rejectedApplications ?? 0 }}</strong></span>
            </a>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12">
        <div class="card-panel">
            <h6>Recent Test Attempts</h6>

            @forelse($recentAttempts ?? [] as $attempt)
            <a href="#" class="recent-row">
                <div class="icon-box"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <div class="title">{{ $attempt->candidate_name }}</div>
                    <div class="subtitle">{{ $attempt->test_title }} &middot; {{ $attempt->date }}</div>
                </div>
            </a>
            @empty
            <p class="text-muted" style="font-size: 0.85rem; padding-top: 10px;">No test attempts yet.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection