@extends('layouts.admin')

@section('title', 'Applications')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">

<style>
    .filter-pills-wrapper {
        display: flex;
        justify-content: center;
        margin-bottom: 24px;
    }

    .filter-pills {
        display: inline-flex;
        background-color: var(--primary);
        padding: 6px;
        border-radius: 14px;
        gap: 4px;
        flex-wrap: wrap;
        box-shadow: 0 8px 18px rgba(23,117,59,0.2);
    }

    .filter-pill {
        padding: 9px 22px;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        border: none;
        color: rgba(255,255,255,0.75);
        transition: all 0.15s ease;
    }

    .filter-pill:hover {
        color: #fff;
    }

    .filter-pill.active {
        background-color: #fff;
        color: var(--primary);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    .profile-filter-select {
        padding: 10px 16px;
        border-radius: 999px;
        border: 1.5px solid var(--border);
        font-size: 0.84rem;
        font-weight: 600;
        color: var(--text-dark);
        background-color: #fff;
        cursor: pointer;
        outline: none;
    }

    .profile-filter-select:focus {
        border-color: var(--primary);
    }

    .profile-status-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background-color: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
        font-size: 0.6rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        margin-top: 4px;
        white-space: nowrap;
    }

    .profile-status-tag i {
        font-size: 0.58rem;
    }

    .applications-search-form {
        position: relative;
        width: 280px;
    }

    .applications-search-form i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    .applications-search-form input {
        width: 100%;
        padding: 10px 16px 10px 38px;
        border-radius: 999px;
        border: 1.5px solid var(--border);
        font-size: 0.84rem;
        color: var(--text-dark);
        outline: none;
        transition: border-color 0.15s ease;
    }

    .applications-search-form input:focus {
        border-color: var(--primary);
    }

    .applications-table-wrapper {
        background-color: #fff;
        border-radius: 14px;
        border: 1px solid var(--border);
        margin-top: 4px;
        max-height: 620px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .applications-table-wrapper thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .applications-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .applications-table thead th {
        background-color: var(--primary);
        color: #fff;
        text-align: left;
        padding: 10px 18px;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        vertical-align: middle;
        line-height: 1.3;
    }

    .applications-table tbody td {
        padding: 14px 18px;
        font-size: 0.86rem;
        color: var(--text-dark);
        border-bottom: 1px solid #f3f4f8;
        vertical-align: middle;
    }

    /* Center-align the Program, Applied On, Status and Actions columns */
    .applications-table th:nth-child(3),
    .applications-table td:nth-child(3),
    .applications-table th:nth-child(4),
    .applications-table td:nth-child(4),
    .applications-table th:nth-child(5),
    .applications-table td:nth-child(5),
    .applications-table th:nth-child(6),
    .applications-table td:nth-child(6) {
        text-align: center;
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
 .action-btn.view { background-color: rgba(62,64,149,0.1); color: var(--primary); }
    .action-btn.cancel { background-color: #f3f4f6; color: var(--text-dark); }

    .priority-tag {
        display: inline-block;
        background-color: var(--bg-soft);
        color: var(--text-muted);
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 5px;
        margin-right: 6px;
        vertical-align: middle;
    }

    .program-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        color: var(--primary);
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0;
    }

    .choice-review-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f8;
        font-size: 0.88rem;
    }

    .choice-review-row:last-child {
        border-bottom: none;
    }

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

    /* ===== APPROVE MODAL: choice picker ===== */
    .approve-note {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 16px;
        line-height: 1.5;
    }

    .approve-choice-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        margin-bottom: 10px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-dark);
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .approve-choice-option:has(input:checked) {
        border-color: #22c55e;
        background-color: rgba(34,197,94,0.06);
    }

    .approve-choice-option input[type="radio"],
    .approve-choice-option input[type="checkbox"] {
        width: 17px;
        height: 17px;
        accent-color: #22c55e;
        cursor: pointer;
        flex-shrink: 0;
    }

    .modal-footer {
        background-color: #fafbfc;
        border-top: 1px solid var(--border);
        padding: 16px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* ===== INFINITE SCROLL LOADER ===== */
    .infinite-scroll-loader {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 22px;
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        visibility: hidden;
    }

    .infinite-scroll-loader.is-active {
        visibility: visible;
    }

    .loader-spinner {
        width: 18px;
        height: 18px;
        border: 2.5px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: infinite-scroll-spin 0.7s linear infinite;
    }

    @keyframes infinite-scroll-spin {
        to { transform: rotate(360deg); }
    }

    .infinite-scroll-end {
        display: none;
        text-align: center;
        padding: 18px;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .infinite-scroll-end.is-active {
        display: block;
    }
        /* ---------- Responsive: table text ek line mein + bara font ---------- */
    @media (max-width: 991.98px) {
        html body .applications-table-wrapper {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        html body .applications-table {
            table-layout: auto !important;
            width: max-content !important;
            min-width: 100% !important;
        }
        /* colgroup ki percentage widths mobile par ignore */
        html body .applications-table colgroup col {
            width: auto !important;
        }
        html body .applications-table thead th,
        html body .applications-table tbody td {
            white-space: nowrap !important;
            overflow-wrap: normal !important;
            word-break: normal !important;
            padding: 14px 18px !important;
        }
        html body .applications-table thead th {
            font-size: 0.85rem !important;
        }
        html body .applications-table tbody td {
            font-size: 1rem !important;
        }
        html body .applications-table .status-badge {
            font-size: 0.85rem !important;
            padding: 6px 14px !important;
        }
        html body .applications-table .program-count-badge {
            font-size: 1rem !important;
        }
        html body .applications-table .action-btn {
            font-size: 0.85rem !important;
            padding: 8px 14px !important;
            white-space: nowrap;
        }
        /* Actions column ke buttons (View / Approve / Reject) ek line mein */
        html body .applications-table td:last-child {
            white-space: nowrap !important;
        }
        html body .applications-table td:last-child form {
            display: inline-block !important;
        }
    }
        /* ---------- Custom profile dropdown: desktop par hidden ---------- */
    .prof-dd { display: none; }

    @media (max-width: 767.98px) {

        /* Search full width (upar), neeche dropdown + button ek row mein */
        html body .section-heading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 10px !important;
        }
        html body .section-heading .applications-search-form {
            flex: 0 0 100% !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        html body .section-heading .applications-search-form input {
            font-size: 16px !important;
            min-height: 48px;
        }

        /* native select hide */
        html body .section-heading select.profile-filter-select.has-dd { display: none !important; }

        /* custom dropdown */
        html body .section-heading .prof-dd {
            display: block !important;
            position: relative;
            flex: 1 1 0 !important;
            min-width: 0;
        }
        html body .prof-dd-btn {
            width: 100%;
            display: flex !important; align-items: center; justify-content: space-between; gap: 8px;
            min-height: 48px; padding: 8px 14px !important;
            font-size: 16px !important; font-weight: 600; color: #16231c;
            background: #fff !important; border: 1.5px solid #cfe8d8 !important; border-radius: 999px !important;
            cursor: pointer; text-align: left;
        }
        html body .prof-dd-btn span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        html body .prof-dd-btn i { font-size: .8rem; transition: transform .2s; }
        html body .prof-dd.open .prof-dd-btn i { transform: rotate(180deg); }

        html body .prof-dd-menu {
            display: none !important;
            position: absolute !important; left: 0 !important; right: auto !important;
            top: calc(100% + 6px);
            min-width: 100%; width: max-content; max-width: calc(100vw - 32px);
            max-height: 50vh; overflow-y: auto;
            margin: 0 !important; padding: 6px !important; list-style: none !important;
            background: #fff; border: 1px solid #d9e2dd; border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15, 36, 25, .18);
            z-index: 1200;
        }
        html body .prof-dd.open .prof-dd-menu { display: block !important; }
        html body .prof-dd-menu li {
            list-style: none !important; padding: 12px; font-size: 16px;
            border-radius: 10px; cursor: pointer; white-space: nowrap;
        }
        html body .prof-dd-menu li:hover,
        html body .prof-dd-menu li:active { background: #f2f8f4; }
        html body .prof-dd-menu li.selected { background: #e6f4ec; color: #0c7347; font-weight: 700; }

        /* Offline / Call Entry button: same row, same height */
        html body .section-heading > button.action-btn {
            flex: 1 1 0 !important;
            width: auto !important;
            min-width: 0;
            min-height: 48px;
            margin: 0 !important;
            padding: 8px 12px !important;
            font-size: 15px !important;
            white-space: nowrap;
            display: inline-flex !important;
            align-items: center; justify-content: center; gap: 6px;
            border-radius: 999px !important;
        }
    }
</style>
@endpush

@section('content')

<!-- <div class="section-heading">
    <h4>Applications</h4>
</div> -->

{{-- Status filter pills — centered, sized to content, sidebar-matched gradient --}}
<div class="filter-pills-wrapper">
    <div class="filter-pills">
        <a href="{{ route('admin.applications.index', array_merge(request()->except(['status','page']), [])) }}" class="filter-pill {{ !$status ? 'active' : '' }}">All</a>
        <a href="{{ route('admin.applications.index', array_merge(request()->except(['status','page']), ['status' => 'pending'])) }}" class="filter-pill {{ $status === 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('admin.applications.index', array_merge(request()->except(['status','page']), ['status' => 'approved'])) }}" class="filter-pill {{ $status === 'approved' ? 'active' : '' }}">Approved</a>
        <a href="{{ route('admin.applications.index', array_merge(request()->except(['status','page']), ['status' => 'rejected'])) }}" class="filter-pill {{ $status === 'rejected' ? 'active' : '' }}">Rejected</a>
    </div>
</div>

<div class="section-heading">
    <form method="GET" action="{{ route('admin.applications.index') }}" class="applications-search-form">
        @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or application no.">
    </form>

    <select id="profileFilterSelect" class="profile-filter-select">
        <option value="" {{ !$profile ? 'selected' : '' }}>All Profiles</option>
        <option value="complete" {{ $profile === 'complete' ? 'selected' : '' }}>Complete Profiles</option>
        <option value="incomplete" {{ $profile === 'incomplete' ? 'selected' : '' }}>Incomplete Profiles</option>
    </select>

    <button type="button" class="action-btn approve" data-bs-toggle="modal" data-bs-target="#offlineEntryModal">
        <i class="fas fa-phone-alt"></i> Offline / Call Entry
    </button>
</div>

@if(session('success'))
<div class="alert alert-success" style="padding:15px;border-radius:8px;margin-bottom:20px;background:#d4edda;color:#155724;">
    {{ session('success') }}
</div>
@endif

<div class="applications-table-wrapper" id="applicationsTableWrapper">
    @if($applications->count())
    <table class="applications-table">
        <colgroup>
            <col style="width: 14%;">
            <col style="width: 20%;">
            <col style="width: 16%;">
            <col style="width: 13%;">
            <col style="width: 12%;">
            <col style="width: 25%;">
        </colgroup>
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
        <tbody id="applicationsTableBody">
    @foreach($applications as $app)
    <tr>
        <td>{{ $app->application_no }}</td>
        <td>
            {{ $app->candidate->fname ?? '' }} {{ $app->candidate->lname ?? '' }}
            @if($app->candidate && !$app->candidate->isProfileComplete())
                <span class="profile-status-tag"><i class="fas fa-exclamation-circle"></i></span>
            @endif
        </td>
        <td>
            @if($app->choices->count())
                <span class="program-count-badge">
                    {{ $app->choices->count() }} Program{{ $app->choices->count() != 1 ? 's' : '' }}
                </span>
            @else
                <span style="color: var(--text-muted);">—</span>
            @endif
        </td>
        <td>{{ \Carbon\Carbon::parse($app->applied_at)->format('d M Y') }}</td>
        <td><span class="status-badge {{ $app->status }}">{{ ucfirst($app->status) }}</span></td>
        <td>
            {{-- View: opens a modal with full candidate detail --}}
            <button type="button" class="action-btn view" data-bs-toggle="modal" data-bs-target="#appModal{{ $app->id }}">
                <i class="fas fa-eye"></i> View
            </button>

            @if($app->status === 'pending')
            {{-- Approve: opens a popup to pick which of the candidate's applied programs to approve --}}
            <button type="button" class="action-btn approve" data-bs-toggle="modal" data-bs-target="#approveModal{{ $app->id }}">
                Approve
            </button>
            <form method="POST" action="{{ route('admin.applications.reject', $app->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="action-btn reject">Reject</button>
            </form>
            @endif
            @if($app->status !== 'pending')
    <a href="{{ route('admin.applications.print', $app->id) }}" target="_blank"
       class="action-btn view" style="text-decoration:none; display:inline-block;">
        <i class="fas fa-print"></i> Print
    </a>
@endif
        </td>
    </tr>
    @endforeach
</tbody>
    </table>

    {{-- Infinite scroll: sentinel element holds the next page URL. Controller must
         preserve the current query string on the paginator (->withQueryString() or
         ->appends(request()->query())) so search/status/profile filters carry over
         into subsequent pages fetched here. --}}
    <div id="applicationsInfiniteLoader"
         class="infinite-scroll-loader"
         data-next-url="{{ $applications->nextPageUrl() }}">
        <div class="loader-spinner"></div>
        <span>Loading more applications...</span>
    </div>
    <div id="applicationsInfiniteEnd" class="infinite-scroll-end">All applications loaded.</div>

    @else
    <p class="empty-note">No applications found{{ $status ? ' with status "'.$status.'"' : '' }}.</p>
    @endif
</div>

{{-- Dynamically-loaded View/Approve modals for applications fetched via infinite scroll get appended here --}}
<div id="dynamicModalsContainer"></div>

{{-- ===================== View Modal (detail only, no actions) ===================== --}}
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
                    @forelse($app->choices as $choice)
                    <div class="choice-review-row">
                        <span class="priority-tag">Priority {{ $choice->priority }}</span>
                        <strong>{{ $choice->program->name ?? '—' }}</strong>
                        <span class="status-badge {{ $choice->admin_status }}" style="margin-left:auto;">{{ ucfirst($choice->admin_status) }}</span>
                    </div>
                    @empty
                    <p class="text-muted" style="font-size: 0.85rem;">No program choices found.</p>
                    @endforelse
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
                        <div><span class="review-label">Mobile No</span>{{ $app->candidate->father_mobile_no ?? '—' }}</div>
                    </div>
                </div>

                <div class="review-section">
                    <h6>Educational Qualification</h6>
                    @forelse($app->candidate->education ?? [] as $edu)
                    <div class="edu-review-card">
                        <strong>{{ $edu->degree }}</strong>
                        <div class="review-grid">
                            <div><span class="review-label">Board / University</span>{{ $edu->boardname ?? '—' }}</div>
                            <div><span class="review-label">Major Subjects</span>{{ $edu->majorsubjects ?: '—' }}</div>
                            <div><span class="review-label">Marks</span>{{ $edu->obtained_marks }} / {{ $edu->total_marks }} ({{ $edu->percentage ?? '—' }}%, {{ $edu->division ?? '—' }})</div>
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
        </div>
    </div>
</div>
{{-- ===================== Approve Modal (pick which of the applied programs) ===================== --}}
@if($app->status === 'pending')
<div class="modal fade" id="approveModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header" style="background-color: var(--primary); color: #fff;">
                <h5 class="modal-title">Approve — {{ $app->application_no }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('admin.applications.approve', $app->id) }}">
                @csrf
                <div class="modal-body">
                    <p class="approve-note">
    Select one or more programs to approve <strong>{{ $app->candidate->fname ?? '' }} {{ $app->candidate->lname ?? '' }}</strong> for.
    Unselected program choices will be rejected automatically.
</p>

                   @foreach($app->choices as $choice)
<label class="approve-choice-option">
    <input type="checkbox" name="choice_ids[]" value="{{ $choice->id }}" {{ $loop->first ? 'checked' : '' }}>
    <span class="priority-tag">P{{ $choice->priority }}</span>
    {{ $choice->program->name ?? '—' }}
</label>
@endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="action-btn approve">Confirm Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endforeach

{{-- ===================== Offline / Call Entry Modal ===================== --}}
<div class="modal fade" id="offlineEntryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden; max-height: 85vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="background-color: var(--primary); color: #fff; flex-shrink: 0;">
                <h5 class="modal-title">Offline / Call Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('admin.applications.offline.store') }}" style="display: flex; flex-direction: column; min-height: 0;">
                @csrf
                <div class="modal-body" style="overflow-y: auto; flex: 1 1 auto; min-height: 0;">
                    <p class="approve-note">
                        Use this when a candidate calls in and can't apply online themselves.
                        A login account and application will be created on their behalf.
                    </p>

                    <div class="review-grid" style="margin-bottom: 14px;">
                        <div>
                            <span class="review-label">First Name</span>
                            <input type="text" name="fname" class="form-control" required>
                        </div>
                        <div>
                            <span class="review-label">Last Name</span>
                            <input type="text" name="lname" class="form-control" required>
                        </div>
                        <div>
                            <span class="review-label">CNIC / B-Form</span>
                            <input type="text" name="cnic" class="form-control" placeholder="12345-1234567-1" required>
                        </div>
                        <div>
                            <span class="review-label">Mobile Number</span>
                            <input type="text" name="mobile_no" class="form-control" placeholder="03XXXXXXXXX" required>
                        </div>
                        <div class="review-full">
                            <span class="review-label">Email</span>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="review-full">
                            <span class="review-label">Intermediate Marks (%)</span>
                            <input type="number" step="0.01" min="0" max="100" name="intermediate_percentage" class="form-control" required>
                        </div>
                    </div>

                    <span class="review-label" style="display:block; margin-bottom:8px;">Select Program(s) — max 3</span>
                    @foreach($programs as $program)
                    <label class="approve-choice-option">
                        <input type="checkbox" name="program_ids[]" value="{{ $program->id }}">
                        {{ $program->name }}
                    </label>
                    @endforeach
                </div>
                <div class="modal-footer" style="flex-shrink: 0;">
                    <button type="button" class="action-btn cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="action-btn approve">Create Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var offlineModal = document.getElementById('offlineEntryModal');
        if (offlineModal) {
            var checkboxes = offlineModal.querySelectorAll('input[name="program_ids[]"]');

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    var checkedCount = offlineModal.querySelectorAll('input[name="program_ids[]"]:checked').length;

                    if (checkedCount > 3) {
                        this.checked = false;
                        alert('You can select a maximum of 3 programs.');
                        return;
                    }

                    checkboxes.forEach(function (cb) {
                        if (!cb.checked) {
                            cb.disabled = checkedCount >= 3;
                        }
                    });
                });
            });
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        var profileSelect = document.getElementById('profileFilterSelect');
        if (profileSelect) {
            profileSelect.addEventListener('change', function () {
                var url = new URL(window.location.href);
                if (this.value) {
                    url.searchParams.set('profile', this.value);
                } else {
                    url.searchParams.delete('profile');
                }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            });
        }
    });

    /* ===================== Infinite scroll for Applications table ===================== */
    document.addEventListener("DOMContentLoaded", function () {
        var loader = document.getElementById('applicationsInfiniteLoader');
        var endNote = document.getElementById('applicationsInfiniteEnd');
        var tbody = document.getElementById('applicationsTableBody');
        var modalsContainer = document.getElementById('dynamicModalsContainer');

        if (!loader || !tbody) return;

        var isLoading = false;

        function finishNoMorePages() {
            loader.dataset.nextUrl = '';
            loader.classList.remove('is-active');
            if (endNote) endNote.classList.add('is-active');
            observer.unobserve(loader);
        }

        function loadNextPage() {
            var nextUrl = loader.dataset.nextUrl;
            if (!nextUrl || nextUrl === 'null' || nextUrl === '' || isLoading) return;

            isLoading = true;
            loader.classList.add('is-active');

            fetch(nextUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(function (res) { return res.text(); })
            .then(function (html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');

                var newTbody = doc.getElementById('applicationsTableBody');
                if (newTbody) {
                    tbody.insertAdjacentHTML('beforeend', newTbody.innerHTML);
                }

                if (modalsContainer) {
                    var newModals = doc.querySelectorAll('[id^="appModal"], [id^="approveModal"]');
                    newModals.forEach(function (modalEl) {
                        if (!document.getElementById(modalEl.id)) {
                            modalsContainer.insertAdjacentHTML('beforeend', modalEl.outerHTML);
                        }
                    });
                }

                var newLoader = doc.getElementById('applicationsInfiniteLoader');
                var newNextUrl = newLoader ? newLoader.dataset.nextUrl : '';

                if (newNextUrl && newNextUrl !== 'null' && newNextUrl !== '') {
                    loader.dataset.nextUrl = newNextUrl;
                } else {
                    finishNoMorePages();
                }

                isLoading = false;
                loader.classList.remove('is-active');
            })
            .catch(function () {
                isLoading = false;
                loader.classList.remove('is-active');
            });
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) loadNextPage();
            });
        }, {
            root: document.getElementById('applicationsTableWrapper'),
            rootMargin: '150px',
            threshold: 0
        });

        if (loader.dataset.nextUrl && loader.dataset.nextUrl !== '') {
            observer.observe(loader);
        } else if (endNote) {
            endNote.classList.add('is-active');
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sel = document.getElementById('profileFilterSelect');
    if (!sel) return;

    var wrap = document.createElement('div');
    wrap.className = 'prof-dd';
    wrap.innerHTML =
        '<button type="button" class="prof-dd-btn" aria-haspopup="listbox">' +
        '<span></span><i class="fas fa-chevron-down"></i></button>' +
        '<ul class="prof-dd-menu" role="listbox"></ul>';

    var btn   = wrap.querySelector('.prof-dd-btn');
    var label = btn.querySelector('span');
    var menu  = wrap.querySelector('.prof-dd-menu');

    Array.prototype.forEach.call(sel.options, function (opt) {
        var li = document.createElement('li');
        li.setAttribute('role', 'option');
        li.textContent = opt.text.trim();
        if (opt.selected) { li.className = 'selected'; label.textContent = li.textContent; }
        li.addEventListener('click', function () {
            wrap.classList.remove('open');
            sel.value = opt.value;
            sel.dispatchEvent(new Event('change', { bubbles: true })); // purana filter redirect chalega
        });
        menu.appendChild(li);
    });

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        wrap.classList.toggle('open');
    });
    document.addEventListener('click', function () { wrap.classList.remove('open'); });

    sel.classList.add('has-dd');
    sel.parentNode.insertBefore(wrap, sel.nextSibling);
});
</script>

@endsection