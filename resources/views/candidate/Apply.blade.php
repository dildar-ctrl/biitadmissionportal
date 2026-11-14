@extends('layouts.app')

@section('title', 'Apply & Submit')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">

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
        background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
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
        padding: 16px 22px;
        border-bottom: 1px solid #f3f4f8;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        transition: background-color 0.15s ease;
    }

    .program-row:hover {
        background-color: #fafbfc;
    }

    .program-row:last-child {
        border-bottom: none;
    }

    .program-row .info {
        flex: 1;
        min-width: 220px;
    }

    .program-row .info strong {
        font-size: 0.95rem;
        color: var(--text-dark);
        font-weight: 600;
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
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
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

    .apply-now-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ---------- Checkbox program selection ---------- */
    .program-checkbox-label {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
        min-width: 220px;
        cursor: pointer;
    }

    .program-checkbox {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 22px;
        height: 22px;
        border: 2px solid #d1d5db;
        border-radius: 7px;
        background-color: #fff;
        cursor: pointer;
        flex-shrink: 0;
        position: relative;
        transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
    }

    .program-checkbox:hover {
        border-color: var(--primary);
    }

    .program-checkbox:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(12, 115, 71, 0.18);
    }

    .program-checkbox:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .program-checkbox:checked::after {
        content: '';
        position: absolute;
        left: 6px;
        top: 2px;
        width: 6px;
        height: 11px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .priority-badge {
      background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .program-row:has(.program-checkbox:checked) {
        background-color: var(--bg-soft);
        box-shadow: inset 3px 0 0 var(--primary);
    }

    .selection-bar {
        position: sticky;
        bottom: 16px;
        background-color: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px 22px;
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .selection-count {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
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
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
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

<div class="search-box">
    <i class="fas fa-search"></i>
    <input type="text" id="programSearch" placeholder="Search programs..." onkeyup="filterPrograms()">
</div>

@if($alreadyapplied && $myApplications->isNotEmpty())
<div class="session-group" style="margin-bottom: 24px;">
    <div class="session-group-header" style="cursor: default;">
        <div class="icon-box"><i class="fas fa-check"></i></div>
        <div class="title">Your Selected Programs</div>
    </div>
    <div class="session-group-body">
        @foreach($myApplications as $choice)
            <div class="program-row">
                <div class="info">
                    <strong>
                        <span class="priority-badge">{{ $choice->priority }}{{ $choice->priority == 1 ? 'st' : ($choice->priority == 2 ? 'nd' : 'rd') }} choice</span>
                        {{ $choice->program->name }}
                    </strong>
                </div>
                <a href="{{ route('apply.status') }}" class="view-link">
                    <i class="fas fa-eye"></i> View
                </a>
            </div>
        @endforeach
    </div>
</div>
@else

<form method="POST" action="{{ route('apply.store') }}" id="programSelectForm">
    @csrf

    <div id="programGroups">
        @forelse($groupedPrograms as $sessionId => $programsInSession)
            <div class="session-group">
                <div class="session-group-header" onclick="toggleGroup(this)">
                    <div class="icon-box"><i class="fas fa-graduation-cap"></i></div>
                    <div class="title">{{ $sessions[$sessionId] ?? 'Session' }}</div>
                    <i class="fas fa-chevron-down chevron"></i>
                </div>

                <div class="session-group-body">
                    @foreach($programsInSession as $program)
                        <div class="program-row" data-title="{{ strtolower($program->name) }}">
                            <label class="program-checkbox-label" for="program_{{ $program->id }}">
                                <input
                                    type="checkbox"
                                    id="program_{{ $program->id }}"
                                    class="program-checkbox"
                                    value="{{ $program->id }}"
                                    data-name="{{ $program->name }}"
                                    onchange="onProgramToggle(this)"
                                    @if(in_array($program->id, $sessionSelectedIds ?? [])) data-preselected="1" @endif
                                >
                                <div class="info">
                                    <strong>{{ $program->name }}</strong>
                                </div>
                            </label>
                            <span class="priority-badge" id="badge_{{ $program->id }}" style="display:none;"></span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-muted" style="font-size: 0.85rem;">No programs are currently open for admission.</p>
        @endforelse

        <p id="noResultsMsg" class="text-muted" style="font-size: 0.85rem; display:none;">No matching programs found.</p>
    </div>

    <div class="selection-bar">
        <div class="selection-count"><span id="selectedCount">0</span>/3 programs selected — the order you tick them sets your 1st, 2nd and 3rd choice.</div>
        <button type="submit" class="apply-now-btn" id="submitSelectionBtn" disabled>
            Submit Selection <i class="fas fa-arrow-right ms-1"></i>
        </button>
    </div>
</form>

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

        document.querySelectorAll('.session-group').forEach(function (group) {
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

    // ---------- Program selection (max 3, order picked = priority) ----------
    const MAX_PROGRAM_CHOICES = 3;
    // Restore selection order if candidate already picked programs earlier
    // in this session (e.g. navigated back from Profile without submitting).
    let selectedProgramIds = @json(array_values($sessionSelectedIds ?? []));

    function renderBadges() {
        document.querySelectorAll('.priority-badge[id^="badge_"]').forEach(function (badge) {
            badge.style.display = 'none';
            badge.textContent = '';
        });

        const labels = ['1ST CHOICE', '2ND CHOICE', '3RD CHOICE'];
        selectedProgramIds.forEach(function (id, index) {
            const badge = document.getElementById('badge_' + id);
            if (badge) {
                badge.textContent = labels[index];
                badge.style.display = 'inline-block';
            }
        });

        document.getElementById('selectedCount').textContent = selectedProgramIds.length;

        const submitBtn = document.getElementById('submitSelectionBtn');
        if (submitBtn) {
            submitBtn.disabled = selectedProgramIds.length === 0;
        }
    }

    function onProgramToggle(checkbox) {
        const id = checkbox.value;

        if (checkbox.checked) {
            if (selectedProgramIds.length >= MAX_PROGRAM_CHOICES) {
                checkbox.checked = false;
                alert('You can select a maximum of ' + MAX_PROGRAM_CHOICES + ' programs.');
                return;
            }
            selectedProgramIds.push(id);
        } else {
            selectedProgramIds = selectedProgramIds.filter(function (existingId) {
                return existingId !== id;
            });
        }

        renderBadges();
    }

    const programSelectForm = document.getElementById('programSelectForm');
    if (programSelectForm) {
        programSelectForm.addEventListener('submit', function () {
            programSelectForm.querySelectorAll('input[name="program_ids[]"]').forEach(function (el) {
                el.remove();
            });

            selectedProgramIds.forEach(function (id) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'program_ids[]';
                hidden.value = id;
                programSelectForm.appendChild(hidden);
            });
        });
    }

    // On load, tick the checkboxes for any programs already saved to session
    // (selectedProgramIds is seeded from the backend above) and show badges.
    // Only runs when the selection form is actually on the page (i.e. not
    // in the "already applied" read-only view).
    if (programSelectForm) {
        selectedProgramIds = selectedProgramIds.map(String).filter(function (id) {
            const checkbox = document.getElementById('program_' + id);
            if (checkbox) {
                checkbox.checked = true;
                return true;
            }
            return false; // program no longer open/available - drop silently
        });
        renderBadges();
    }
</script>
@endpush