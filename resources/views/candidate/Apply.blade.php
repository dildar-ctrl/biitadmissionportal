@extends('admin.layout')

@section('title', 'Apply & Submit')

@push('styles')
<style>
    .search-box {
        position: relative;
        margin-bottom: 20px;
    }

    .search-box i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    .search-box input {
        width: 100%;
        height: 48px;
        padding: 10px 16px 10px 44px;
        border: 1.5px solid var(--border);
        border-radius: 999px;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .search-box input:focus {
        border-color: var(--primary);
    }

    /* ---------- Session group (gradient header + collapsible list) ---------- */
    .session-group {
        background-color: #fff;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 18px;
        border: 1px solid var(--border);
    }

    .session-group-header {
        background: linear-gradient(135deg, #3e4095, #06b6d4);
        color: #fff;
        padding: 16px 22px;
        display: flex;
        align-items: center;
        gap: 14px;
        cursor: pointer;
        user-select: none;
    }

    .session-group-header .icon-box {
        width: 36px;
        height: 36px;
        background-color: rgba(255,255,255,0.2);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .session-group-header .title {
        font-weight: 700;
        font-size: 1rem;
        flex: 1;
    }

    .session-group-header .chevron {
        transition: transform 0.2s ease;
    }

    .session-group-header.collapsed .chevron {
        transform: rotate(-90deg);
    }

    .program-row {
        padding: 18px 22px;
        border-bottom: 1px solid #f3f4f8;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .program-row:last-child {
        border-bottom: none;
    }

    .program-row .info {
        flex: 1;
        min-width: 220px;
    }

    .program-row .info strong {
        display: block;
        font-size: 0.95rem;
        color: var(--text-dark);
        margin-bottom: 6px;
    }

    .program-row .instruction-note {
        font-size: 0.8rem;
        color: #c0392b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .last-date-pill {
        background-color: #fdf3e3;
        color: #b7791f;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 999px;
        white-space: nowrap;
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

    .apply-now-btn {
        background: linear-gradient(135deg, #3e4095, #06b6d4);
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        white-space: nowrap;
    }

    .apply-now-btn:hover {
        opacity: 0.92;
    }

    .view-link {
        color: var(--primary);
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .view-link:hover {
        text-decoration: underline;
    }

    /* ---------- My Applications table ---------- */
    .applications-table-wrapper {
        background-color: #fff;
        border-radius: 14px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-top: 30px;
    }

    .applications-table-wrapper h6 {
        padding: 18px 22px;
        margin: 0;
        font-weight: 800;
        color: var(--text-dark);
        border-bottom: 1px solid var(--border);
    }

    .applications-table {
        width: 100%;
        border-collapse: collapse;
    }

    .applications-table thead th {
        background: linear-gradient(135deg, #3e4095, #06b6d4);
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

    .download-btn-sm {
        background-color: var(--primary);
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
    }

    .download-btn-sm:hover {
        background-color: var(--primary-dark);
        color: #fff;
    }
</style>
@endpush

@section('content')

<div class="stepper-title">Admissions Open For</div>

@if(!$candidate)
<div class="card-panel text-center" style="padding: 40px;">
    <p style="color: var(--text-muted);">Please complete your Profile before applying to a program.</p>
    <a href="{{ route('profile.edit') }}" class="apply-now-btn" style="text-decoration:none; display:inline-block;">Go to Profile</a>
</div>
@else

<div class="search-box">
    <i class="fas fa-search"></i>
    <input type="text" id="programSearch" placeholder="Search programs..." onkeyup="filterPrograms()">
</div>

<div id="programGroups">
    @forelse($programs->groupBy('session') as $session => $programsInSession)
        @php
            // Already-applied programs float to the top of the list within
            // this session, so the candidate doesn't have to hunt for them.
            $programsInSession = $programsInSession->sortByDesc(function ($program) use ($myApplications) {
                return $myApplications->has($program->id) ? 1 : 0;
            });
        @endphp
        <div class="session-group">
            <div class="session-group-header" onclick="toggleGroup(this)">
                <div class="icon-box"><i class="fas fa-graduation-cap"></i></div>
                <div class="title">{{ $session }}</div>
                <i class="fas fa-chevron-down chevron"></i>
            </div>

            <div class="session-group-body">
                @foreach($programsInSession as $program)
                    @php $existing = $myApplications->get($program->id); @endphp

                    <div class="program-row" data-title="{{ strtolower($program->title) }}">
                        <div class="info">
                            <strong>{{ $program->title }}</strong>
                            @if(!$existing)
                            <div class="instruction-note">
                                <i class="fas fa-clock"></i>
                                Please review your profile and documents carefully before proceeding with your application.
                            </div>
                            @endif
                        </div>

                        @if($existing)
                            <a href="{{ route('apply.status') }}" class="view-link">
                                <i class="fas fa-eye"></i> View
                            </a>
                        @else
                            <form method="POST" action="{{ route('apply.store') }}">
                                @csrf
                                <input type="hidden" name="program_id" value="{{ $program->id }}">
                                <button type="submit" class="apply-now-btn">Apply Now <i class="fas fa-arrow-right ms-1"></i></button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-muted" style="font-size: 0.85rem;">No programs are currently open for admission.</p>
    @endforelse

    <p id="noResultsMsg" class="text-muted" style="font-size: 0.85rem; display:none;">No matching programs found.</p>
</div>

@endif

@endsection

@push('scripts')
<script>
    function filterPrograms() {
        const query = document.getElementById('programSearch').value.toLowerCase();
        const rows = document.querySelectorAll('.program-row');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const matches = row.dataset.title.includes(query);
            row.style.display = matches ? 'flex' : 'none';
            if (matches) visibleCount++;
        });

        // hide a session group entirely if none of its rows match
        document.querySelectorAll('.session-group').forEach(function (group) {
            const visibleRows = group.querySelectorAll('.program-row[style*="flex"], .program-row:not([style])');
            const anyVisible = Array.from(group.querySelectorAll('.program-row')).some(r => r.style.display !== 'none');
            group.style.display = anyVisible ? 'block' : 'none';
        });

        document.getElementById('noResultsMsg').style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function toggleGroup(header) {
        header.classList.toggle('collapsed');
        const body = header.nextElementSibling;
        body.style.display = header.classList.contains('collapsed') ? 'none' : 'block';
    }
</script>
@endpush