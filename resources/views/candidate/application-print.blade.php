<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admission Form - BIIT</title>
    <style>
        * {
            font-family: 'Times New Roman', Times, serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            padding: 30px 40px;
            color: #000;
            background: #fff;
        }

        /* ===== FORM NO (TOP LEFT) ===== */
        .form-no {
            text-align: left;
            font-size: 0.95rem;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .form-no .line {
            border-bottom: 1px solid #000;
            display: inline-block;
            padding: 0 6px 2px;
            min-width: 100px;
        }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding-bottom: 0px;
            margin-bottom: 0px;
            gap: 15px;
        }

        /* --- LEFT: LOGO ONLY, aligned same as photo box --- */
        .header-left {
            flex-shrink: 0;
            text-align: center;
            margin-top: 20px;
        }

        .header-left .logo {
            width: 95px;
            height: 95px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .header-left .logo-placeholder {
            width: 95px;
            height: 95px;
            border-radius: 50%;
            border: 2px solid #0c7347;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: #0c7347;
            background: #f5f5f5;
            margin: 0 auto;
            line-height: 1.1;
        }

        /* --- CENTER: TITLE --- */
        .header-center {
            flex: 1;
            text-align: center;
            padding: 6px 10px 0;
        }

        .header-center .institute-name {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .header-center .affiliation {
            font-size: 0.85rem;
            font-weight: 500;
            margin: 4px 0 0;
        }

        .header-center .affiliation-bold {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 2px;
        }

        .header-center .title {
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .header-center .title-sub {
            font-size: 0.95rem;
            font-weight: 700;
            margin-top: 12px;
        }

        /* --- RIGHT: PHOTO, aligned same as logo --- */
        .header-right {
            flex-shrink: 0;
            text-align: center;
            margin-top: 20px;
        }

        .header-right .photo-box {
            width: 110px;
            height: 130px;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 2px;
            background: #fff;
        }

        .header-right .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ===== PROGRAMS LIST (plain text, NOT bold) ===== */
        .programs-list {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            margin: 4px 0 14px;
            font-size: 0.95rem;
            font-weight: 400;
        }

        .programs-row {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .programs-list .selected {
           
        }

        /* ===== SECTION TITLES (Center) ===== */
        .section-title {
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            margin: 18px 0 10px;
            text-decoration: underline;
            text-align: center;
        }

        .sub-section-title {
            font-weight: 700;
            font-size: 0.92rem;
            margin: 12px 0 8px;
        }

        /* ===== FIELD LINES ===== */
        .field-line {
            display: flex;
            align-items: baseline;
            margin-bottom: 4px;
            font-size: 0.92rem;
        }

        .field-line .label {
            display: inline-block;
            min-width: 210px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .field-line .value {
            border-bottom: 1px solid #000;
            flex: 1;
            padding: 2px 6px;
            min-height: 22px;
        }

        /* Row that holds 2 or 3 field-lines side by side */
        .field-row {
            display: flex;
            gap: 25px;
            margin-bottom: 4px;
        }

        .field-row .field-line {
            flex: 1;
            margin-bottom: 0;
            min-width: 0;
        }

        .field-row .field-line .label {
            min-width: unset;
            margin-right: 4px;
        }

        .field-row .field-line .value {
            min-width: 40px;
            white-space: nowrap;
            overflow: hidden;
        }

        .field-row .field-line.marks-field {
            flex: 1.6;
        }

        /* Address fields (13 & 14) - label on its own line, value gets 2 lines */
        .field-block {
            margin-bottom: 8px;
            font-size: 0.92rem;
        }

        .field-block .label-tight {
            min-width: unset;
            margin-right: 6px;
        }

        .field-block .value-line {
            border-bottom: 1px solid #000;
            min-height: 20px;
            padding: 2px 6px;
        }

        .academic-fields {
            padding-left: 30px;
        }

        .academic-fields .field-line .label {
            min-width: 170px;
        }

        .academic-fields .field-row .field-line .label {
            min-width: unset;
        }

        /* ===== CHECKLIST (3 columns, matches reference image) ===== */
        .checklist {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 6px 20px;
            padding: 5px 0 10px;
        }

        .checklist-item {
            display: flex;
            align-items: center;
            font-size: 0.88rem;
        }

        .checklist-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            accent-color: #000;
        }

        .checklist-item label {
            margin: 0;
        }
/* ===== DOCUMENTS (admin print only) ===== */
.doc-page {
    break-before: page;
    page-break-before: always;
    text-align: center;
    padding-top: 10px;
}
.doc-title {
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 12px;
    text-transform: uppercase;
    text-decoration: underline;
}
.doc-page img,
.pdf-holder canvas {
    max-width: 100%;
    max-height: 245mm;
    width: auto;
    height: auto;
    display: block;
    margin: 0 auto;
}
.pdf-holder canvas + canvas {
    break-before: page;
    page-break-before: always;
    margin-top: 10px;
}
.doc-error { font-size: 0.9rem; color: #b91c1c; }
.print-btn:disabled { opacity: 0.6; cursor: wait; }

        /* ===== PRINT BUTTON ===== */
        .print-btn-wrapper {
            text-align: center;
            margin-top: 30px;
        }

        .print-btn {
            background: #0c7347;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-family: Arial, sans-serif;
            font-size: 1rem;
            cursor: pointer;
        }

        /* ===== PRINT ===== */
        @media print {
            .print-btn-wrapper {
                display: none;
            }
            body {
                padding: 20px 30px;
            }
            .checklist-item input[type="checkbox"] {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* ===== RESPONSIVE ===== */
        @media screen and (max-width: 800px) {
            body {
                padding: 20px 15px;
            }
            .header {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
            }
            .header-left {
                order: 1;
                margin-top: 0;
            }
            .header-center {
                order: 2;
                flex-basis: 100%;
                padding: 5px 0;
            }
            .header-right {
                order: 3;
                margin-top: 0;
            }
            .header-center .institute-name,
            .header-center .affiliation-bold {
                font-size: 1.1rem;
            }
            .header-left .logo,
            .header-left .logo-placeholder {
                width: 70px;
                height: 70px;
            }
            .header-right .photo-box {
                width: 90px;
                height: 110px;
            }
            .programs-row {
                gap: 25px;
            }
            .programs-list .program-item,
            .programs-row {
                font-size: 0.82rem;
            }
            .field-row {
                flex-direction: column;
                gap: 6px;
            }
            .field-line {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 6px;
            }
            .field-line .label {
                min-width: auto;
                margin-bottom: 2px;
                white-space: normal;
            }
            .academic-fields {
                padding-left: 15px;
            }
            .academic-fields .field-line .label {
                min-width: 140px;
            }
            .checklist {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media screen and (max-width: 500px) {
            body {
                padding: 10px 8px;
            }
            .header-center .institute-name,
            .header-center .affiliation-bold {
                font-size: 0.95rem;
            }
            .header-center .title {
                font-size: 0.80rem;
            }
            .header-left .logo,
            .header-left .logo-placeholder {
                width: 55px;
                height: 55px;
            }
            .header-right .photo-box {
                width: 70px;
                height: 90px;
                font-size: 0.7rem;
            }
            .programs-row {
                flex-direction: column;
                align-items: center;
                gap: 4px;
            }
            .field-line {
                font-size: 0.82rem;
            }
            .checklist {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- ========================================== -->
    <!-- FORM NO (TOP LEFT) -->
    <!-- ========================================== -->
    <div class="form-no">
        Form No: <span class="line">{{ $application->application_no ?? 'BIIT20260001' }}</span>
    </div>

    <!-- ========================================== -->
    <!-- HEADER: LOGO LEFT | TITLE CENTER | PHOTO RIGHT -->
    <!-- ========================================== -->
    <div class="header">
        <!-- LEFT: LOGO ONLY, aligned same level as photo box -->
        <div class="header-left">
            <img src="{{ asset('logo.png') }}" alt="BIIT Logo" class="logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-placeholder" style="display: none;">BIIT<br>1998</div>
        </div>

        <!-- CENTER: TITLE -->
        <div class="header-center">
            <div class="institute-name">Barani Institute of Information Technology</div>
            <div class="affiliation">Affiliated with</div>
            <div class="affiliation affiliation-bold">PMAS - Arid Agriculture University, Rawalpindi</div>
            <div class="title">APPLICATION FOR ADMISSION</div>
            <div class="title-sub">Degree Programs</div>
        </div>

        <!-- RIGHT: PHOTO, aligned same level as logo -->
        <div class="header-right">
            <div class="photo-box">
                @php
                    $photoDoc = null;
                    if ($application->candidate && $application->candidate->documents) {
                        $photoDoc = $application->candidate->documents->where('documenttype_id', 7)->first();
                    }
                @endphp
                @if ($photoDoc && Storage::disk('public')->exists($photoDoc->document_path))
                    <img src="{{ asset('storage/' . $photoDoc->document_path) }}" alt="PHOTO">
                @else
                    PHOTO
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PROGRAMS LIST (plain text, not bold, matches reference image) -->
    <!-- ========================================== -->
    @php
        // Candidate's actual selected programs, in priority order (1st, 2nd, 3rd choice)
        $selectedProgramNames = $application->choices
            ->sortBy('priority')
            ->map(fn ($choice) => $choice->program->name ?? '')
            ->filter()
            ->values();

        $programRows = $selectedProgramNames->chunk(2);
    @endphp

    <div class="programs-list">
        @foreach ($programRows as $row)
        <div class="programs-row">
            @foreach ($row as $programName)
                <span class="selected">{{ $programName }}</span>
            @endforeach
        </div>
        @endforeach
    </div>

    <!-- ========================================== -->
    <!-- PERSONAL INFORMATION -->
    <!-- ========================================== -->
    <div class="section-title">PERSONAL INFORMATION</div>

    <div>

        <!-- 1: own line -->
        <div class="field-line">
            <span class="label">1. Applicant's Name (Capital Letters):</span>
            <span class="value">{{ strtoupper(($application->candidate->fname ?? '') . ' ' . ($application->candidate->lname ?? '')) }}</span>
        </div>

        <!-- 2 & 3: same line -->
        <div class="field-row">
            <div class="field-line">
                <span class="label">2. Applicant's Contact No:</span>
                <span class="value">{{ $application->candidate->mobile_no ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">3. E-Mail:</span>
                <span class="value">{{ $application->candidate->email ?? '' }}</span>
            </div>
        </div>

        <!-- 4, 5 & 6: same line -->
        <div class="field-row">
            <div class="field-line">
                <span class="label">4. CNIC:</span>
                <span class="value">{{ $application->candidate->cnic ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">5. Sex (M/F):</span>
                <span class="value">{{ $application->candidate->gender ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">6. Marital Status:</span>
                <span class="value">{{ $application->candidate->marital_status ?? '' }}</span>
            </div>
        </div>

        <!-- 7: own line -->
        <div class="field-line">
            <span class="label">7. PMAS-AAUR Registration No. (If previously enrolled):</span>
            <span class="value">{{ $application->candidate->PMASREGNO ?? '' }}</span>
        </div>

        <!-- 8 & 9: same line -->
        <div class="field-row">
            <div class="field-line">
                <span class="label">8. Applicant's Date of Birth:</span>
                <span class="value">{{ $application->candidate->dob ? \Carbon\Carbon::parse($application->candidate->dob)->format('d-M-Y') : '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">9. Religion:</span>
                <span class="value">{{ $application->candidate->religion ?? '' }}</span>
            </div>
        </div>

        <!-- 10 & 11: same line -->
        <div class="field-row">
            <div class="field-line">
                <span class="label">10. Nationality:</span>
                <span class="value">{{ $application->candidate->nationality ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">11. Any Medical Issue:</span>
                <span class="value">{{ $application->candidate->medicalissue ?? '' }}</span>
            </div>
        </div>

        <!-- 12 i & ii: same line -->
        <div class="field-row">
            <div class="field-line">
                <span class="label">12. i) Father/Guardian Name:</span>
                <span class="value">{{ $application->candidate->father_name ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">ii) Father/Guardian CNIC:</span>
                <span class="value">{{ $application->candidate->father_cnic ?? '' }}</span>
            </div>
        </div>

        <!-- 12 iii & iv: same line -->
        <div class="field-row">
            <div class="field-line">
                <span class="label">iii) Father/Guardian Contact No:</span>
                <span class="value">{{ $application->candidate->father_mobile_no ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">iv) Relationship with Guardian:</span>
                <span class="value">{{ $application->candidate->relationshipwithguardian ?? 'Father' }}</span>
            </div>
        </div>

        <!-- 13: label & value on same line, plus one extra blank line for continuation -->
        <div class="field-block">
            <div class="field-line">
                <span class="label label-tight">13. Postal Address:</span>
                <span class="value">{{ $application->candidate->postal_address ?? '' }}</span>
            </div>
            <div class="value-line"></div>
        </div>

        <!-- 14: label & value on same line, plus one extra blank line for continuation -->
        <div class="field-block">
            <div class="field-line">
                <span class="label label-tight">14. Permanent Address:</span>
                <span class="value">{{ $application->candidate->permanent_address ?? '' }}</span>
            </div>
            <div class="value-line"></div>
        </div>

        <!-- 15 i & ii: same line -->
        <div class="field-row">
            <div class="field-line">
                <span class="label">15. i) Emergency Contact Name:</span>
                <span class="value">{{ $application->candidate->emergencycontactname ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">ii) Relationship:</span>
                <span class="value">{{ $application->candidate->emergencycontactrelationship ?? '' }}</span>
            </div>
        </div>

        <!-- 15 iii: own line -->
        <div class="field-line">
            <span class="label">iii) Contact No:</span>
            <span class="value">{{ $application->candidate->emergencycontact ?? '' }}</span>
        </div>

        <!-- 16: own line -->
        <div class="field-line">
            <span class="label">16. Sports &amp; Co-curricular Activities (if any):</span>
            <span class="value">{{ $application->candidate->activities ?? '' }}</span>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- ACADEMIC RECORD -->
    <!-- ========================================== -->
    <div class="section-title">ACADEMIC RECORD</div>

    @php
        $matric = null;
        $inter = null;
        if ($application->candidate && $application->candidate->education) {
            foreach ($application->candidate->education as $edu) {
                $degree = strtolower($edu->degree ?? '');
                if (str_contains($degree, 'matric') || str_contains($degree, 'o level')) {
                    $matric = $edu;
                } elseif (str_contains($degree, 'intermediate') || str_contains($degree, 'fsc') || str_contains($degree, 'a level') || str_contains($degree, 'hssc')) {
                    $inter = $edu;
                }
            }
        }
    @endphp

    <!-- Matric -->
    <div class="sub-section-title">Matriculation or Equivalent Examination:</div>

    <div class="academic-fields">
        <!-- a & b: same line -->
        <div class="field-row">
            <div class="field-line">
                <span class="label">a) Name of Board:</span>
                <span class="value">{{ $matric->boardname ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">b) Passing Year:</span>
                <span class="value">{{ $matric ? \Carbon\Carbon::parse($matric->date_of_passing)->format('Y') : '' }}</span>
            </div>
        </div>

        <!-- c, d & e: same line -->
        <div class="field-row">
            <div class="field-line marks-field">
                <span class="label">c) Marks Obtained/out of:</span>
                <span class="value">{{ $matric->obtained_marks ?? '' }} / {{ $matric->total_marks ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">d) Division/Grade:</span>
                <span class="value">{{ $matric->division ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">e) Percentage:</span>
                <span class="value">
                    @if ($matric)
                        @if ($matric->percentage)
                            {{ $matric->percentage }}%
                        @elseif ($matric->total_marks > 0)
                            {{ number_format(($matric->obtained_marks / $matric->total_marks) * 100, 1) }}%
                        @endif
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Intermediate -->
    <div class="sub-section-title">Intermediate or Equivalent Examination:</div>

    <div class="academic-fields">
        <!-- a: own line -->
        <div class="field-line">
            <span class="label">a) Major Subjects:</span>
            <span class="value">{{ $inter->majorsubjects ?? '' }}</span>
        </div>

        <!-- b: own line -->
        <div class="field-line">
            <span class="label">b) Name of Board:</span>
            <span class="value">{{ $inter->boardname ?? '' }}</span>
        </div>

        <!-- c, d & e: same line -->
        <div class="field-row">
            <div class="field-line">
                <span class="label">c) Roll No:</span>
                <span class="value">{{ $inter->roll_no ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">d) Registration No:</span>
                <span class="value">{{ $inter->registrationno ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">e) Passing Year:</span>
                <span class="value">{{ $inter ? \Carbon\Carbon::parse($inter->date_of_passing)->format('Y') : '' }}</span>
            </div>
        </div>

        <!-- f, g & h: same line -->
        <div class="field-row">
            <div class="field-line marks-field">
                <span class="label">f) Marks Obtained/out of:</span>
                <span class="value">{{ $inter->obtained_marks ?? '' }} / {{ $inter->total_marks ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">g) Division/Grade:</span>
                <span class="value">{{ $inter->division ?? '' }}</span>
            </div>
            <div class="field-line">
                <span class="label">h) Percentage:</span>
                <span class="value">
                    @if ($inter)
                        @if ($inter->percentage)
                            {{ $inter->percentage }}%
                        @elseif ($inter->total_marks > 0)
                            {{ number_format(($inter->obtained_marks / $inter->total_marks) * 100, 1) }}%
                        @endif
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- DOCUMENTS CHECK LIST (3 columns, matches reference image) -->
    <!-- ========================================== -->
    <div class="section-title">Documents Check List:</div>

    @php
        $documents = [
            'Matric Results',
            'Matric Character Certificate',
            'B- Form/CNIC (Applicant)',
            'Intermediate Result',
            'Intermediate Character Certificate',
            'CNIC Father/Guardian',
            'Latest Picture (Passport Size)',
        ];

        $docMapping = [
            'Matric Results' => 3,
            'Matric Character Certificate' => 2,
            'B- Form/CNIC (Applicant)' => 1,
            'Intermediate Result' => 5,
            'Intermediate Character Certificate' => 4,
            'CNIC Father/Guardian' => 6,
            'Latest Picture (Passport Size)' => 7,
        ];

        $uploadedDocs = [];
        if ($application->candidate && $application->candidate->documents) {
            $uploadedDocs = $application->candidate->documents->pluck('documenttype_id')->toArray();
        }
    @endphp

    <div class="checklist">
        @foreach ($documents as $docName)
            @php $docTypeId = $docMapping[$docName] ?? 0; @endphp
            <div class="checklist-item">
                <input type="checkbox" id="doc_{{ $loop->index }}"
                    {{ in_array($docTypeId, $uploadedDocs) ? 'checked' : '' }}
                    disabled>
                <label for="doc_{{ $loop->index }}">{{ $docName }}</label>
            </div>
        @endforeach
    </div>
@if(!empty($includeDocuments))
    @foreach(($application->candidate->documents ?? collect())->where('documenttype_id', '!=', 7)->sortBy('documenttype_id') as $doc)
        @php
            $ext = strtolower(pathinfo($doc->document_path, PATHINFO_EXTENSION));
            $fileUrl = asset('storage/' . $doc->document_path);
        @endphp
        <div class="doc-page">
            <div class="doc-title">{{ $doc->documentType->type ?? 'Document' }}</div>
            @if($ext === 'pdf')
                <div class="pdf-holder" data-pdf-url="{{ $fileUrl }}"></div>
            @else
                <img src="{{ $fileUrl }}" alt="{{ $doc->documentType->type ?? 'Document' }}">
            @endif
        </div>
    @endforeach
@endif
    <!-- ========================================== -->
    <!-- PRINT BUTTON -->
    <!-- ========================================== -->
    <div class="print-btn-wrapper">
<button class="print-btn" id="printBtn" onclick="window.print()"
        {{ !empty($includeDocuments) ? 'disabled' : '' }}>
    {{ !empty($includeDocuments) ? 'Loading documents...' : '🖨 Print / Save as PDF' }}
</button>    </div>
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">
@endpush
@if(!empty($includeDocuments))
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    var btn = document.getElementById('printBtn');

    function renderPdf(holder) {
        return pdfjsLib.getDocument(holder.dataset.pdfUrl).promise.then(function (pdf) {
            var chain = Promise.resolve();
            for (var n = 1; n <= pdf.numPages; n++) {
                (function (pageNo) {
                    chain = chain.then(function () {
                        return pdf.getPage(pageNo).then(function (page) {
                            var viewport = page.getViewport({ scale: 2 });
                            var canvas = document.createElement('canvas');
                            canvas.width = viewport.width;
                            canvas.height = viewport.height;
                            holder.appendChild(canvas);
                            return page.render({
                                canvasContext: canvas.getContext('2d'),
                                viewport: viewport
                            }).promise;
                        });
                    });
                })(n);
            }
            return chain;
        }).catch(function () {
            holder.innerHTML = '<p class="doc-error">This PDF could not be rendered.</p>';
        });
    }

    var waits = [];
    document.querySelectorAll('.doc-page img').forEach(function (img) {
        waits.push(img.complete ? Promise.resolve() : new Promise(function (r) {
            img.onload = img.onerror = r;
        }));
    });
    document.querySelectorAll('.pdf-holder').forEach(function (h) { waits.push(renderPdf(h)); });

    Promise.all(waits).then(function () {
        btn.disabled = false;
        btn.textContent = '🖨 Print / Save as PDF';
    });
})();
</script>
@endif
</body>
</html>