@extends('layouts.admin')

@section('title', 'Candidates')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">

<style>
    .search-box {
        position: relative;
        margin-bottom: 20px;
        max-width: 420px;
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
        height: 46px;
        padding: 10px 16px 10px 44px;
        border: 1.5px solid var(--border);
        border-radius: 999px;
        font-size: 0.88rem;
        outline: none;
    }

    .search-box input:focus {
        border-color: var(--primary);
    }

    .candidates-table-wrapper {
        background-color: #fff;
        border-radius: 14px;
        border: 1px solid var(--border);
        max-height: 620px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .candidates-table-wrapper thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .candidates-table {
        width: 100%;
        border-collapse: collapse;
    }

    .candidates-table thead th {
        background-color: var(--primary);
        color: #fff;
        text-align: left;
        padding: 12px 18px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .candidates-table tbody td {
        padding: 14px 18px;
        font-size: 0.86rem;
        color: var(--text-dark);
        border-bottom: 1px solid #f3f4f8;
        vertical-align: middle;
    }

    .candidates-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-badge.complete { background-color: rgba(34,197,94,0.12); color: #22c55e; }
    .status-badge.incomplete { background-color: rgba(245,158,11,0.12); color: #f59e0b; }

    .action-btn {
        border: none;
        padding: 7px 14px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.76rem;
        cursor: pointer;
    }

    .action-btn.view { background-color: rgba(62,64,149,0.1); color: var(--primary); }

    .empty-note {
        font-size: 0.85rem;
        color: var(--text-muted);
        padding: 30px;
        text-align: center;
    }

    /* Review modal internals — shared look with the Applications review modal */
    .review-section { margin-bottom: 20px; }
    .review-section h6 {
        color: var(--primary);
        font-weight: 800;
        font-size: 0.9rem;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border);
    }
    .review-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px 20px;
        font-size: 0.85rem;
    }
    .review-grid .review-full { grid-column: 1 / -1; }
    .review-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 2px;
    }
    .edu-review-card {
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 10px;
        font-size: 0.85rem;
    }
    .edu-review-card:last-child { margin-bottom: 0; }
    .doc-review-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f3f4f8;
        font-size: 0.85rem;
    }
    .doc-review-row:last-child { border-bottom: none; }
    .doc-review-row a {
        color: var(--primary);
        font-weight: 700;
        text-decoration: none;
        font-size: 0.8rem;
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
        html body .candidates-table-wrapper {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        html body .candidates-table {
            width: max-content !important;
            min-width: 100% !important;
        }
        html body .candidates-table thead th,
        html body .candidates-table tbody td {
            white-space: nowrap !important;
            overflow-wrap: normal !important;
            word-break: normal !important;
            padding: 14px 18px !important;
        }
        html body .candidates-table thead th {
            font-size: 0.85rem !important;
        }
        html body .candidates-table tbody td {
            font-size: 1rem !important;
        }
        html body .candidates-table .status-badge {
            font-size: 0.85rem !important;
            padding: 6px 14px !important;
        }
        html body .candidates-table .action-btn {
            font-size: 0.85rem !important;
            padding: 8px 14px !important;
            white-space: nowrap;
        }
    }
    
</style>
@endpush

@section('content')

<div class="section-heading">
    <h4>Candidates</h4>
</div>

{{-- Search by name, CNIC, email, or mobile number --}}
<form method="GET" action="{{ route('admin.candidates.index') }}" class="search-box">
    <i class="fas fa-search"></i>
    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, CNIC, email, or mobile...">
</form>

<div class="candidates-table-wrapper" id="candidatesTableWrapper">
    @if($candidates->count())
    <table class="candidates-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>CNIC</th>
                <th>Email</th>
                <th>Mobile No</th>
                <th>Profile Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="candidatesTableBody">
            @foreach($candidates as $login)
            <tr>
                <td>{{ $login->fname }} {{ $login->lname }}</td>
                <td>{{ $login->cnic }}</td>
                <td>{{ $login->email }}</td>
                <td>{{ $login->mobile_no }}</td>
                <td>
                    @if($login->candidate)
                        <span class="status-badge complete">Profile Complete</span>
                    @else
                        <span class="status-badge incomplete">Not Completed</span>
                    @endif
                </td>
                <td>
                    <button type="button" class="action-btn view" data-bs-toggle="modal" data-bs-target="#candidateModal{{ $login->id }}">
                        <i class="fas fa-eye"></i> View
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Infinite scroll: sentinel element holds the next page URL. Controller must
         preserve the current query string on the paginator (->withQueryString() or
         ->appends(request()->query())) so the "search" filter carries over into
         subsequent pages fetched here. --}}
    <div id="candidatesInfiniteLoader"
         class="infinite-scroll-loader"
         data-next-url="{{ $candidates->nextPageUrl() }}">
        <div class="loader-spinner"></div>
        <span>Loading more candidates...</span>
    </div>
    <div id="candidatesInfiniteEnd" class="infinite-scroll-end">All candidates loaded.</div>

    @else
    <p class="empty-note">No candidates found{{ $search ? ' matching "'.$search.'"' : '' }}.</p>
    @endif
</div>

{{-- Dynamically-loaded candidate modals fetched via infinite scroll get appended here --}}
<div id="dynamicModalsContainer"></div>

{{-- ===================== Candidate Detail Modals ===================== --}}
@foreach($candidates as $login)
<div class="modal fade" id="candidateModal{{ $login->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header" style="background-color: var(--primary); color: #fff;">
                <h5 class="modal-title">{{ $login->fname }} {{ $login->lname }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                @if(!$login->candidate)

                <p class="text-muted" style="font-size: 0.9rem;">
                    This candidate has signed up but hasn't completed their profile yet.
                </p>
                <div class="review-grid">
                    <div><span class="review-label">CNIC</span>{{ $login->cnic }}</div>
                    <div><span class="review-label">Mobile No</span>{{ $login->mobile_no }}</div>
                    <div class="review-full"><span class="review-label">Email</span>{{ $login->email }}</div>
                </div>

                @else

                <div class="review-section">
                    <h6>Personal Information</h6>
                    <div class="review-grid">
                        <div><span class="review-label">CNIC</span>{{ $login->candidate->cnic ?? '—' }}</div>
                        <div><span class="review-label">Date of Birth</span>{{ $login->candidate->dob ?? '—' }}</div>
                        <div><span class="review-label">Gender</span>{{ $login->candidate->gender ?? '—' }}</div>
                        <div><span class="review-label">Mobile No</span>{{ $login->candidate->mobile_no ?? '—' }}</div>
                        <div><span class="review-label">Email</span>{{ $login->candidate->email ?? '—' }}</div>
                        <div><span class="review-label">Religion</span>{{ $login->candidate->religion ?? '—' }}</div>
                        <div class="review-full"><span class="review-label">Present Address</span>{{ $login->candidate->postal_address ?? '—' }}</div>
                        <div class="review-full"><span class="review-label">Permanent Address</span>{{ $login->candidate->permanent_address ?? '—' }}</div>
                    </div>
                </div>

                <div class="review-section">
                    <h6>Father's Details</h6>
                    <div class="review-grid">
                        <div><span class="review-label">Name</span>{{ $login->candidate->father_name ?? '—' }}</div>
                        <div><span class="review-label">CNIC</span>{{ $login->candidate->father_cnic ?? '—' }}</div>
                        <div><span class="review-label">Mobile No</span>{{ $login->candidate->father_mobile_no ?? '—' }}</div>
                    </div>
                </div>

                <div class="review-section">
                    <h6>Educational Qualification</h6>
                    @forelse($login->candidate->education ?? [] as $edu)
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
                    @forelse($login->candidate->documents ?? [] as $doc)
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

                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    /* ===================== Infinite scroll for Candidates table ===================== */
    document.addEventListener("DOMContentLoaded", function () {
        var loader = document.getElementById('candidatesInfiniteLoader');
        var endNote = document.getElementById('candidatesInfiniteEnd');
        var tbody = document.getElementById('candidatesTableBody');
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

                var newTbody = doc.getElementById('candidatesTableBody');
                if (newTbody) {
                    tbody.insertAdjacentHTML('beforeend', newTbody.innerHTML);
                }

                if (modalsContainer) {
                    var newModals = doc.querySelectorAll('[id^="candidateModal"]');
                    newModals.forEach(function (modalEl) {
                        if (!document.getElementById(modalEl.id)) {
                            modalsContainer.insertAdjacentHTML('beforeend', modalEl.outerHTML);
                        }
                    });
                }

                var newLoader = doc.getElementById('candidatesInfiniteLoader');
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
            root: document.getElementById('candidatesTableWrapper'),
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

@endsection