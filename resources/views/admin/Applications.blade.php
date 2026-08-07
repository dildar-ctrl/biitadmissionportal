@extends('admin.layout')

@section('title', 'Applications')

@push('styles')
<style>
    .filter-pills {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-pill {
        padding: 8px 18px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        border: 1.5px solid var(--border);
        color: var(--text-muted);
    }

    .filter-pill.active {
        background-color: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

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
        background-color: var(--primary);
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
        vertical-align: middle;
    }

    .applications-table tbody tr:last-child td {
        border-bottom: none;
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

    .action-btn {
        border: none;
        padding: 7px 14px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.76rem;
        cursor: pointer;
        margin-right: 6px;
    }

    .action-btn.approve { background-color: rgba(34,197,94,0.12); color: #22c55e; }
    .action-btn.reject { background-color: rgba(239,68,68,0.12); color: #ef4444; }
    .action-btn.view { background-color: rgba(59,130,246,0.12); color: #3b82f6; }

    .empty-note {
        font-size: 0.85rem;
        color: var(--text-muted);
        padding: 30px;
        text-align: center;
    }

    /* ===== MODAL REVIEW STYLING ===== */
    .modal-body {
        background-color: #fafbfc;
        padding: 28px;
    }

    .review-section {
        background-color: #fff;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 16px;
        border: 1px solid var(--border);
    }

    .review-section h6 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--primary);
        margin: 0 0 14px 0;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .review-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    @media (max-width: 600px) {
        .review-grid {
            grid-template-columns: 1fr;
        }
    }

    .review-grid > div {
        display: flex;
        flex-direction: column;
    }

    .review-grid .review-full {
        grid-column: 1 / -1;
    }

    .review-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 5px;
        display: block;
    }

    .review-grid > div > *:not(.review-label) {
        font-size: 0.88rem;
        color: var(--text-dark);
        line-height: 1.5;
        word-break: break-word;
    }

    .review-line {
        font-size: 0.88rem;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.6;
    }

    .edu-review-card {
        background-color: #f9fafb;
        border-left: 3px solid var(--primary);
        padding: 14px;
        border-radius: 6px;
        margin-bottom: 12px;
    }

    .edu-review-card strong {
        display: block;
        font-size: 0.9rem;
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .edu-review-card .review-grid {
        margin: 0;
    }

    .doc-review-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f8;
        font-size: 0.88rem;
    }

    .doc-review-row:last-child {
        border-bottom: none;
    }

    .doc-review-row span {
        color: var(--text-dark);
        font-weight: 500;
    }

    .doc-review-row a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .doc-review-row a:hover {
        text-decoration: underline;
    }

    .modal-footer {
        background-color: #fafbfc;
        border-top: 1px solid var(--border);
        padding: 16px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .modal-footer form {
        display: inline;
    }
</style>
@endpush

@section('content')

<div class="section-heading">
    <h4>Applications</h4>

    {{-- Generates a Roll Slip for every approved application that doesn't already have one --}}
    <form method="POST" action="{{ route('admin.rollslip.generateAll') }}" onsubmit="return confirm('Generate Roll Slips for all approved applications that don\'t have one yet?');">
        @csrf
        <button type="submit" class="pill-select" style="border:none; ">
            <i class="fas fa-id-card me-1"></i> Generate All Roll Slips
        </button>
    </form>
</div>

{{-- Status filter pills — link back to the same page with a different ?status= query --}}
<div class="filter-pills">
    <a href="{{ route('admin.applications.index') }}" class="filter-pill {{ !$status ? 'active' : '' }}">All</a>
    <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}" class="filter-pill {{ $status === 'pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('admin.applications.index', ['status' => 'approved']) }}" class="filter-pill {{ $status === 'approved' ? 'active' : '' }}">Approved</a>
    <a href="{{ route('admin.applications.index', ['status' => 'rejected']) }}" class="filter-pill {{ $status === 'rejected' ? 'active' : '' }}">Rejected</a>
</div>

@if(session('success'))
<div class="alert alert-success" style="padding:15px;border-radius:8px;margin-bottom:20px;background:#d4edda;color:#155724;">
    {{ session('success') }}
</div>
@endif

<div class="applications-table-wrapper">
    @if($applications->count())
    <table class="applications-table">
        <thead>
            <tr>
                <th>Application No.</th>
                <th>Candidate</th>
                <th>Program</th>
                <th>Applied On</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr>
                <td>{{ $app->application_no }}</td>
                <td>{{ $app->candidate->fname ?? '' }} {{ $app->candidate->lname ?? '' }}</td>
                <td>
                    {{ $app->program->title ?? '' }}<br>
                    <small style="color: var(--text-muted);">{{ $app->program->session ?? '' }}</small>
                </td>
                <td>{{ \Carbon\Carbon::parse($app->applied_at)->format('d M Y') }}</td>
                <td><span class="status-badge {{ $app->status }}">{{ ucfirst($app->status) }}</span></td>
                <td>
                    {{-- View opens a modal with full candidate details for admin review --}}
                    <button type="button" class="action-btn view" data-bs-toggle="modal" data-bs-target="#appModal{{ $app->id }}">
                        <i class="fas fa-eye"></i> View
                    </button>

                    @if($app->status === 'pending')
                    <form method="POST" action="{{ route('admin.applications.approve', $app->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="action-btn approve">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.applications.reject', $app->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="action-btn reject">Reject</button>
                    </form>
                    @elseif($app->status === 'approved')
                        @if($app->rollSlip)
                        <a href="{{ route('admin.rollslip.show', $app->id) }}" target="_blank" class="action-btn view">
                            <i class="fas fa-id-card"></i> View Roll Slip
                        </a>
                        @else
                        <form method="POST" action="{{ route('admin.rollslip.generate', $app->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="action-btn approve">Generate Roll Slip</button>
                        </form>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="padding: 16px 22px;">
        {{ $applications->links() }}
    </div>
    @else
    <p class="empty-note">No applications found{{ $status ? ' with status "'.$status.'"' : '' }}.</p>
    @endif
</div>

{{-- ===================== Review Modals ===================== --}}
{{-- Rendered outside the table (a <div class="modal"> can't legally sit inside <tbody>) —
     one modal per application, opened by the "View" button in the Actions column. --}}
@foreach($applications as $app)
<div class="modal fade" id="appModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header" style="background-color: var(--primary); color: #fff;">
                <h5 class="modal-title">{{ $app->candidate->fname ?? '' }} {{ $app->candidate->lname ?? '' }} — {{ $app->application_no }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="review-section">
                    <h6>Applying For</h6>
                    <p class="review-line"><strong>{{ $app->program->title ?? '' }}</strong> &middot; {{ $app->program->session ?? '' }}</p>
                </div>

                <div class="review-section">
                    <h6>Personal Information</h6>
                    <div class="review-grid">
                        <div><span class="review-label">CNIC</span>{{ $app->candidate->cnic ?? '—' }}</div>
                        <div><span class="review-label">Date of Birth</span>{{ $app->candidate->dob ?? '—' }}</div>
                        <div><span class="review-label">Gender</span>{{ $app->candidate->gender ?? '—' }}</div>
                        <div><span class="review-label">Mobile No</span>{{ $app->candidate->mobile_no ?? '—' }}</div>
                        <div><span class="review-label">Email</span>{{ $app->candidate->email ?? '—' }}</div>
                        <div><span class="review-label">Religion</span>{{ $app->candidate->religion ?? '—' }}</div>
                        <div class="review-full"><span class="review-label">Present Address</span>{{ $app->candidate->postal_address ?? '—' }}</div>
                        <div class="review-full"><span class="review-label">Permanent Address</span>{{ $app->candidate->permanent_address ?? '—' }}</div>
                    </div>
                </div>

                <div class="review-section">
                    <h6>Father's Details</h6>
                    <div class="review-grid">
                        <div><span class="review-label">Name</span>{{ $app->candidate->father_name ?? '—' }}</div>
                        <div><span class="review-label">CNIC</span>{{ $app->candidate->father_cnic ?? '—' }}</div>
                        <div><span class="review-label">Occupation</span>{{ $app->candidate->father_occupation ?? '—' }}</div>
                        <div><span class="review-label">Mobile No</span>{{ $app->candidate->father_mobile_no ?? '—' }}</div>
                    </div>
                </div>

                <div class="review-section">
                    <h6>Educational Qualification</h6>
                    @forelse($app->candidate->education ?? [] as $edu)
                    <div class="edu-review-card">
                        <strong>{{ $edu->degree }}</strong>
                        <div class="review-grid">
                            <div><span class="review-label">Board / University</span>{{ $edu->boardUniversity->title ?? '—' }}</div>
                            <div><span class="review-label">Selected Subject</span>{{ $edu->selected_subject ?? '—' }}</div>
                            <div><span class="review-label">Marks</span>{{ $edu->obtained_marks }} / {{ $edu->total_marks }} ({{ $edu->marksType->marks_type ?? '' }})</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted" style="font-size: 0.85rem;">No education records.</p>
                    @endforelse
                </div>

                <div class="review-section">
                    <h6>Uploaded Documents</h6>
                    @forelse($app->candidate->documents ?? [] as $doc)
                    <div class="doc-review-row">
                        <span>{{ $doc->documentType->type ?? 'Document' }}</span>
                        <a href="{{ route('document.file', $doc->id) }}" target="_blank">
                            <i class="fas fa-eye"></i> View File
                        </a>
                    </div>
                    @empty
                    <p class="text-muted" style="font-size: 0.85rem;">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>

            @if($app->status === 'pending')
            <div class="modal-footer">
                <form method="POST" action="{{ route('admin.applications.reject', $app->id) }}">
                    @csrf
                    <button type="submit" class="action-btn reject">Reject</button>
                </form>
                <form method="POST" action="{{ route('admin.applications.approve', $app->id) }}">
                    @csrf
                    <button type="submit" class="action-btn approve">Approve</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endforeach

@endsection