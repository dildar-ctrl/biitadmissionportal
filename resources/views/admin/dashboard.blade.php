@extends('layouts.admin')

@section('title', 'Dashboard')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">
    <style>
    /* =====================================================
       Custom session dropdown — replaces native <select> on
       every screen where the sidebar layout is not the full
       desktop layout (phone, phablet, tablet/iPad, small laptop).
       Native <select> popups can't be positioned with CSS, so
       below this width we always use the custom dropdown.
       Desktop (>1199px) keeps the plain native select.
       ===================================================== */
    .sess-dd { display: none; }

    @media (max-width: 1199.98px) {
        html body .section-heading {
            display: flex !important;
            flex-direction: row !important;
            justify-content: flex-end !important;
            align-items: center !important;
        }
        html body .section-heading > h4:empty { display: none !important; }
        html body .section-heading select.pill-select.has-dd { display: none !important; }

        html body .section-heading .sess-dd {
            display: block !important;
            position: relative;
            margin-left: auto;
            width: 240px;
            max-width: 58%;
        }
        html body .sess-dd-btn {
            width: 100% !important;
            display: flex !important; align-items: center; justify-content: space-between; gap: 8px;
            min-height: 44px; padding: 8px 14px !important;
            font-size: 16px !important; font-weight: 600; color: #16231c;
            background: #fff !important; border: 1px solid #cfe8d8 !important; border-radius: 999px !important;
            cursor: pointer; text-align: left;
        }
        html body .sess-dd-btn span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        html body .sess-dd-btn i { font-size: .8rem; transition: transform .2s; flex-shrink: 0; }
        html body .sess-dd.open .sess-dd-btn i { transform: rotate(180deg); }

        html body .sess-dd-menu {
            display: none !important;
            position: absolute !important; right: 0 !important; left: auto !important;
            top: calc(100% + 6px);
            min-width: 100%; width: max-content; max-width: calc(100vw - 32px);
            max-height: 50vh; overflow-y: auto;
            margin: 0 !important; padding: 6px !important; list-style: none !important;
            background: #fff; border: 1px solid #d9e2dd; border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15, 36, 25, .18);
            z-index: 1200;
        }
        html body .sess-dd.open .sess-dd-menu { display: block !important; }
        html body .sess-dd-menu li {
            list-style: none !important; padding: 10px 12px; font-size: 16px;
            border-radius: 10px; cursor: pointer;
        }
        html body .sess-dd-menu li:hover,
        html body .sess-dd-menu li:active { background: #f2f8f4; }
        html body .sess-dd-menu li.selected { background: #e6f4ec; color: #0c7347; font-weight: 700; }
    }

    /* Small phone: dropdown a bit wider so long session names fit */
    @media (max-width: 420px) {
        html body .section-heading .sess-dd { max-width: 70%; }
    }

    /* =====================================================
       Stat cards — 4-per-row on desktop and tablet/iPad
       (where they already fit comfortably), 2-per-row only
       on phones/phablets where 4 columns get too cramped.
       ===================================================== */
    @media (max-width: 767.98px) {
        html body .row > .col-lg-3.col-md-6 {
            flex: 0 0 auto !important;
            width: 50% !important;
            max-width: 50% !important;
            display: flex !important;
        }
        html body .row:has(> .col-lg-3.col-md-6) {
            --bs-gutter-x: .7rem;
            --bs-gutter-y: .7rem;
        }
        html body .stat-card {
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
            padding: 12px !important;
        }
        html body .stat-card .icon-box { width: 36px !important; height: 36px !important; font-size: .95rem !important; }
        html body .stat-card .stat-text { width: 100% !important; }
        html body .stat-card .label  { font-size: .75rem !important; }
        html body .stat-card .value  { font-size: 1.3rem !important; }
        html body .stat-card .growth { font-size: .72rem !important; }
    }

    /* =====================================================
       Card panel header (Recent Applications + View All):
       keep title and button on one line at every width where
       the sidebar collapses to a top bar.
       ===================================================== */
    @media (max-width: 991.98px) {
        html body .card-panel-header {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 10px;
        }
        html body .card-panel-header h6 {
            margin: 0;
            white-space: nowrap;
            font-size: 1rem;
        }
        html body .card-panel-header a.pill-select {
            width: auto !important;
            min-width: 0 !important;
            max-width: none !important;
            flex: 0 0 auto;
            min-height: 0 !important;
            padding: 6px 16px !important;
            font-size: 0.8rem !important;
            line-height: 1.2;
            white-space: nowrap;
            display: inline-flex !important;
            align-items: center;
        }
    }
    </style>
@endpush
@section('content')

<div class="section-heading">
    <h4></h4>
    <select class="pill-select" onchange="window.location.href='{{ route('admin.dashboard') }}?session_id=' + this.value">
        <option value="">All Sessions</option>
        @foreach($sessions as $session)
            <option value="{{ $session->id }}" {{ (string) $selectedSessionId === (string) $session->id ? 'selected' : '' }}>
                {{ $session->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- ===================== Stat Cards ===================== --}}
<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="icon-box" style="background-color: #e6f4ec; color: #0c7347;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-text">
                <div class="label">Total Candidates</div>
                <div class="value">{{ $totalCandidates ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="icon-box" style="background-color: #e6f4ec; color: #0c7347;">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-text">
                <div class="label">Total Applications</div>
                <div class="value">{{ $totalApplications ?? 0 }}</div>
                @if(($applicationsGrowth ?? 0) >= 0)
                    <div class="growth up"><i class="fas fa-arrow-up"></i> {{ $applicationsGrowth ?? 0 }}%</div>
                @else
                    <div class="growth down"><i class="fas fa-arrow-down"></i> {{ abs($applicationsGrowth ?? 0) }}%</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="icon-box" style="background-color: #e6f4ec; color: #0c7347;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-text">
                <div class="label">Pending Applications</div>
                <div class="value">{{ $pendingApplications ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="icon-box" style="background-color: #e6f4ec; color: #0c7347;">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-text">
                <div class="label">Open Programs</div>
                <div class="value">{{ $totalOpenPrograms ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== Charts ===================== --}}
<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card-panel">
            <div class="card-panel-header">
                <h6>Application Overview</h6>
                
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="applicationOverviewChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-panel">
            <div class="card-panel-header">
                <h6>Applications by Program</h6>
            </div>
            @if(($applicationsByProgram ?? collect())->count())
                <div style="position: relative; height: 160px; display: flex; justify-content: center; align-items: center;">
                    <canvas id="applicationsByProgramChart"></canvas>
                </div>
                <div class="program-list">
                    @foreach($applicationsByProgram as $i => $row)
                    <div class="status-row">
                        <span>
                            <span class="dot" style="background-color: {{ ['#0c7347','#10a562','#6ee7b7','#a7f3d0','#d1fae5'][$i] ?? '#d1fae5' }};"></span>
                            <span class="program-name-text" title="{{ $row->program_name }}">{{ $row->program_name }}</span>
                        </span>
                        <span class="right"><strong>{{ $row->total }}</strong></span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted" style="font-size: 0.85rem;">No program choices yet.</p>
            @endif
        </div>
    </div>
</div>

{{-- ===================== Recent Applications + Quick Actions ===================== --}}
<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card-panel">
            <div class="card-panel-header">
                <h6>Recent Applications</h6>
<a href="{{ route('admin.applications.index', $selectedSessionId ? ['session_id' => $selectedSessionId] : []) }}" class="pill-select" style="text-decoration:none;padding:6px 16px;font-size:0.78rem;">View All</a>            </div>

            @if(($recentApplications ?? collect())->count())
            <table class="simple-table">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Application No.</th>
                        <th>Applied On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentApplications as $app)
                    <tr>
                        <td>{{ $app->email }}</td>
                        <td>{{ $app->application_no }}</td>
                        <td>{{ \Carbon\Carbon::parse($app->applied_at)->format('d M Y') }}</td>
                        <td><span class="status-badge {{ $app->status }}">{{ ucfirst($app->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted" style="font-size: 0.85rem;">No applications yet.</p>
            @endif
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-panel">
            <div class="card-panel-header">
                <h6>Quick Actions</h6>
            </div>
            <div class="row g-2">
                <div class="col-6">
                   <a href="{{ route('admin.applications.index', $selectedSessionId ? ['session_id' => $selectedSessionId] : []) }}" class="quick-action">
                        <div class="icon-box"><i class="fas fa-file-alt"></i></div>
                        <span>View Applications</span>
                    </a>
                </div>
                <div class="col-6">
                   <a href="{{ route('admin.candidates.index', $selectedSessionId ? ['session_id' => $selectedSessionId] : []) }}" class="quick-action">
                        <div class="icon-box"><i class="fas fa-users"></i></div>
                        <span>View Candidates</span>
                    </a>
                </div>
                <div class="col-6">
<a href="{{ route('admin.applications.index', array_filter(['status' => 'pending', 'session_id' => $selectedSessionId])) }}" class="quick-action">                        <div class="icon-box"><i class="fas fa-hourglass-half"></i></div>
                        <span>Review Pending</span>
                    </a>
                </div>
                <div class="col-6">
<a href="{{ route('admin.applications.index', array_filter(['status' => 'approved', 'session_id' => $selectedSessionId])) }}" class="quick-action">                        <div class="icon-box"><i class="fas fa-check-circle"></i></div>
                        <span>Approved List</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== Application Status breakdown ===================== --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card-panel">
            <div class="card-panel-header">
                <h6>Application Status</h6>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'pending', 'session_id' => $selectedSessionId])) }}" class="status-row">
                        <span><span class="dot" style="background-color:#f59e0b;"></span>Pending</span>
                        <span class="right"><strong>{{ $pendingApplications ?? 0 }}</strong></span>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'approved', 'session_id' => $selectedSessionId])) }}" class="status-row">
                        <span><span class="dot" style="background-color:#22c55e;"></span>Approved</span>
                        <span class="right"><strong>{{ $approvedApplications ?? 0 }}</strong></span>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.applications.index', array_filter(['status' => 'rejected', 'session_id' => $selectedSessionId])) }}" class="status-row">
                        <span><span class="dot" style="background-color:#ef4444;"></span>Rejected</span>
                        <span class="right"><strong>{{ $rejectedApplications ?? 0 }}</strong></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var labelsData = {!! json_encode($monthlyLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug']) !!};
        var applicationsData = {!! json_encode($monthlyApplications ?? [0, 0, 0, 0, 0, 0, 0, 3]) !!};
        
        var programNames = {!! json_encode(($applicationsByProgram ?? collect())->pluck('program_name')) !!};
        var programTotals = {!! json_encode(($applicationsByProgram ?? collect())->pluck('total')) !!};

        // Line Chart
        var overviewCtx = document.getElementById('applicationOverviewChart');
        if (overviewCtx) {
            var ctx = overviewCtx.getContext('2d');
            var gradient = ctx.createLinearGradient(0, 0, 0, 220);
            gradient.addColorStop(0, 'rgba(12,115,71,0.25)');
            gradient.addColorStop(1, 'rgba(12,115,71,0)');

            new Chart(overviewCtx, {
                type: 'line',
                data: {
                    labels: labelsData,
                    datasets: [{
                        data: applicationsData,
                        borderColor: '#0c7347',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#0c7347',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f0f4f2' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Donut Chart
        var donutCtx = document.getElementById('applicationsByProgramChart');
        if (donutCtx) {
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: programNames,
                    datasets: [{
                        data: programTotals,
                        backgroundColor: ['#0c7347', '#10a562', '#6ee7b7', '#a7f3d0', '#d1fae5'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sel = document.querySelector('.section-heading select.pill-select');
    if (!sel) return;

    var wrap = document.createElement('div');
    wrap.className = 'sess-dd';
    wrap.innerHTML =
        '<button type="button" class="sess-dd-btn" aria-haspopup="listbox">' +
        '<span></span><i class="fas fa-chevron-down"></i></button>' +
        '<ul class="sess-dd-menu" role="listbox"></ul>';

    var btn   = wrap.querySelector('.sess-dd-btn');
    var label = btn.querySelector('span');
    var menu  = wrap.querySelector('.sess-dd-menu');

    Array.prototype.forEach.call(sel.options, function (opt) {
        var li = document.createElement('li');
        li.setAttribute('role', 'option');
        li.textContent = opt.text.trim();
        if (opt.selected) { li.className = 'selected'; label.textContent = li.textContent; }
        li.addEventListener('click', function () {
            wrap.classList.remove('open');
            sel.value = opt.value;
            sel.dispatchEvent(new Event('change', { bubbles: true })); // purana redirect chalega
        });
        menu.appendChild(li);
    });

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        wrap.classList.toggle('open');
    });
    document.addEventListener('click', function () { wrap.classList.remove('open'); });

    sel.classList.add('has-dd');
    sel.parentNode.appendChild(wrap);
});
</script>
@endpush