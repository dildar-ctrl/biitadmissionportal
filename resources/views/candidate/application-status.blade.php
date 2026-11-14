@extends('layouts.app')

@section('title', 'My Applications')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">

<style>
    .applications-table-wrapper {
        background-color: #fff;
        border-radius: 14px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .applications-table {
        width: 100%;
        border-collapse: collapse;
    }

    .applications-table thead th {
        background-color: #0d965b;
        color: #fff;
        text-align: left;
        padding: 12px 18px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .applications-table tbody td {
        padding: 14px 18px;
        font-size: 0.86rem;
        color: var(--text-dark);
        border-bottom: 1px solid #f3f4f8;
    }

    .applications-table tbody tr:last-child td {
        border-bottom: none;
    }

    .applications-table .program-name {
        font-weight: 700;
    }

    .applications-table .program-session {
        font-size: 0.76rem;
        color: var(--text-muted);
    }

    .status-badge {
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-badge.pending { background-color: rgba(245,158,11,0.12); color: #f59e0b; }
    .status-badge.approved { background-color: rgba(34,197,94,0.12); color: #22c55e; }
    .status-badge.rejected { background-color: rgba(239,68,68,0.12); color: #ef4444; }
    .status-badge.submitted { background-color: rgba(34,197,94,0.12); color: #22c55e; }

    .download-btn-sm {
       background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.78rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-right: 6px;
        margin-bottom: 4px;
    }

    .download-btn-sm:hover {
        background-color: var(--primary-dark);
        color: #fff;
    }

    .empty-note {
        font-size: 0.85rem;
        color: var(--text-muted);
        padding: 30px;
        text-align: center;
    }
</style>
@endpush

@section('content')

<div class="stepper-title">My Applications</div>

<div class="applications-table-wrapper">
    @if($myApplications->count())
    <table class="applications-table">
        <thead>
            <tr>
                <th>Choice</th>
                <th>Application No.</th>
                <th>Program Name</th>
                <th>Application Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($myApplications as $app)
            <tr>
                <td><span class="status-badge" style="background-color: rgba(12,115,71,0.1); color: var(--primary);">{{ $app->priority }}{{ $app->priority == 1 ? 'st' : ($app->priority == 2 ? 'nd' : 'rd') }}</span></td>
                <td>{{ $app->application->application_no ?? '' }}</td>
                <td>
                    <div class="program-name">{{ $app->program->name }}</div>
                    <div class="program-session">{{ $app->program->session->name ?? '' }}</div>
                </td>
                <td><span class="status-badge {{ $app->admin_status }}">{{ ucfirst($app->admin_status) }}</span></td>
                <td>
                    <a href="{{ route('apply.download', $app->id) }}" target="_blank" class="download-btn-sm">
                        <i class="fas fa-download"></i> Download
                    </a>

                    @if($latestApplication->rollSlip ?? null)
                    <a href="{{ route('candidate.rollslip.show', $app->id) }}" target="_blank" class="download-btn-sm">
                        <i class="fas fa-id-card"></i> Roll Slip
                    </a>
                    @endif

                    @if($latestApplication->has_published_result ?? false)
                    <a href="{{ route('candidate.result.show', $app->id) }}" class="download-btn-sm">
                        <i class="fas fa-poll"></i> View Result
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="empty-note">You haven't applied to any programs yet.</p>
    @endif
</div>

@endsection