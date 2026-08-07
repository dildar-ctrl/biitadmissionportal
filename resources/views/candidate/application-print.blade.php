<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application - {{ $application->application_no }}</title>
    <style>
        * { font-family: 'Times New Roman', Times, serif; box-sizing: border-box; }
        body { padding: 40px 50px; color: #000; }

        .app-no {
            font-size: 0.95rem;
            margin-bottom: 10px;
            white-space: nowrap;
        }
        .app-no .line {
            border-bottom: 1px solid #000;
            display: inline-block;
            padding: 0 6px 2px;
        }

        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            gap: 16px;
        }

        .header-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-shrink: 0;
        }

        .header-left img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
        }

        .header-titles {
            flex: 1;
            text-align: center;
        }

        .header-titles h1 {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
            white-space: nowrap;
        }

        .header-titles .subtitle {
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .photograph-box {
            width: 2in;
            height: 2in;
            border: 1.5px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .photograph-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .program-line {
            font-weight: 700;
            margin: 18px 0 26px;
            text-align: center;
        }
        .program-line .fill {
            font-weight: 400;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 320px;
            padding-left: 6px;
        }

        /* ---------- Section titles ---------- */
        .section-title {
            text-align: center;
            font-weight: 700;
            text-decoration: underline;
            margin: 26px 0 14px;
            font-size: 1.05rem;
        }

        /* ---------- Personal Information: two boxes ---------- */
        .info-boxes {
            display: flex;
            gap: 24px;
            margin-bottom: 24px;
        }

        .info-box {
            flex: 1;
        }

        .info-box .box-heading {
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: underline;
            margin-bottom: 12px;
        }

        /* ---------- Aligned form fields ---------- */
        .field-line {
            display: flex;
            align-items: flex-end;
            margin-bottom: 14px;
            font-size: 0.95rem;
        }

        .field-line .label {
            display: inline-block;
            width: 190px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .field-line .fill {
            border-bottom: 1px solid #000;
            flex: 1;
            padding-left: 6px;
            min-height: 20px;
        }

        /* Inside the boxed columns, labels are shorter so the box stays narrow */
        .info-box .field-line {
            margin-bottom: 12px;
        }
        .info-box .field-line .label {
            width: auto;
            min-width: 110px;
        }

        .field-row {
            display: flex;
            gap: 30px;
            margin-bottom: 14px;
        }

        .field-row .field-line {
            margin-bottom: 0;
            flex: 1;
        }

        .field-row .field-line .label {
            width: auto;
            min-width: 90px;
        }

        /* ---------- Address section ---------- */
        .address-section {
            margin-bottom: 24px;
        }

        .address-section .box-heading {
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: underline;
            margin-bottom: 12px;
        }

        /* ---------- Academic record ---------- */

        table.academic-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        table.academic-table th,
        table.academic-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
        }

        table.academic-table th {
            font-weight: 700;
        }

        .print-btn-wrapper {
            text-align: center;
            margin-top: 30px;
        }

        .print-btn {
            background: #3e4095;
            color: #fff;
            border: none;
            padding: 10px 26px;
            border-radius: 6px;
            font-family: 'Open Sans', Arial, sans-serif;
            cursor: pointer;
        }

        /* ---------- Responsive (tablet) ---------- */
        @media screen and (max-width: 900px) {
            body { padding: 30px 24px; }

            .header-titles h1 { font-size: 1.5rem; }
            .header-titles .subtitle { font-size: 0.9rem; }
            .header-left img { width: 100px; height: 100px; }
            .photograph-box { width: 100px; height: 110px; }

            .field-row { flex-direction: column; gap: 10px; }
            .field-line .label { width: 160px; }
            .info-boxes { flex-direction: column; }
        }

        /* ---------- Responsive (mobile) ---------- */
        @media screen and (max-width: 560px) {
            body { padding: 20px 16px; }

            .header-row { flex-wrap: wrap; justify-content: center; text-align: center; }
            .header-left { order: 1; }
            .header-titles { order: 2; padding-top: 8px; flex-basis: 100%; }
            .photograph-box { order: 3; margin: 0 auto; }

            .header-left img { width: 80px; height: 80px; }
            .header-titles h1 { font-size: 1.25rem; margin-bottom: 10px; }
            .header-titles .subtitle { font-size: 0.8rem; }

            .program-line .fill { min-width: 160px; }

            .field-line { flex-direction: column; align-items: flex-start; }
            .field-line .label { width: auto; margin-bottom: 2px; }
            .field-row { flex-direction: column; gap: 10px; }
            .info-boxes { flex-direction: column; gap: 16px; }
        }

        @media print {
            .print-btn-wrapper { display: none; }
            body { padding: 20px 40px; }
            .info-boxes { flex-direction: row !important; }
            .header-row { flex-wrap: nowrap !important; }
        }
    </style>
</head>
<body>

    <div class="app-no">
        App No: <span class="line">{{ $application->application_no }}</span>
    </div>

    <div class="header-row">
        <div class="header-left">
            <img src="{{ asset('logo.png') }}" alt="Northern University Logo">
        </div>

        <div class="header-titles">
            <h1>NORTHERN UNIVERSITY</h1>
            <div class="subtitle">APPLICATION FOR ADMISSION</div>
        </div>

        {{-- Photograph Box with Image --}}
        <div class="photograph-box">
            @php
                // Find the photograph document (documenttype_id = 2)
                $photoDoc = $application->candidate->documents->where('documenttype_id', 2)->first();
            @endphp
            @if($photoDoc && Storage::disk('public')->exists($photoDoc->document_path))
                <img src="{{ asset('storage/' . $photoDoc->document_path) }}" alt="Photograph">
            @else
                <span style="font-size:0.8rem;color:#999;">No Photo</span>
            @endif
        </div>
    </div>

    <div class="program-line">
        Program of Study: <span class="fill">{{ $application->program->title }}</span>
    </div>

    <div class="section-title">Personal Information</div>

    <div class="info-boxes">
        <!-- Candidate's own details -->
        <div class="info-box">
            <div class="box-heading">Applicant Details</div>

            <div class="field-line">
                <span class="label">Name:</span>
                <span class="fill">{{ $application->candidate->fname }} {{ $application->candidate->lname }}</span>
            </div>
            <div class="field-line">
                <span class="label">NIC No.:</span>
                <span class="fill">{{ $application->candidate->cnic }}</span>
            </div>
            <div class="field-line">
                <span class="label">Sex:</span>
                <span class="fill">{{ $application->candidate->gender }}</span>
            </div>
            <div class="field-line">
                <span class="label">Date of Birth:</span>
                <span class="fill">{{ \Carbon\Carbon::parse($application->candidate->dob)->format('d-M-Y') }}</span>
            </div>
            <div class="field-line">
                <span class="label">Mobile No:</span>
                <span class="fill">{{ $application->candidate->mobile_no }}</span>
            </div>
            <div class="field-line">
                <span class="label">E-mail:</span>
                <span class="fill">{{ $application->candidate->email }}</span>
            </div>
        </div>

        <!-- Father's details -->
        <div class="info-box">
            <div class="box-heading">Father's Details</div>

            <div class="field-line">
                <span class="label">Name:</span>
                <span class="fill">{{ $application->candidate->father_name }}</span>
            </div>
            <div class="field-line">
                <span class="label">NIC No.:</span>
                <span class="fill">{{ $application->candidate->father_cnic }}</span>
            </div>
            <div class="field-line">
                <span class="label">Occupation:</span>
                <span class="fill">{{ $application->candidate->father_occupation }}</span>
            </div>
            <div class="field-line">
                <span class="label">Mobile No:</span>
                <span class="fill">{{ $application->candidate->father_mobile_no }}</span>
            </div>
            <div class="field-line">
                <span class="label">E-mail:</span>
                <span class="fill">{{ $application->candidate->father_email }}</span>
            </div>
        </div>
    </div>

    <div class="address-section">
        <div class="box-heading">Address</div>

        <div class="field-line">
            <span class="label">Present Address:</span>
            <span class="fill">{{ $application->candidate->postal_address }}</span>
        </div>

        <div class="field-line">
            <span class="label">Permanent Address:</span>
            <span class="fill">{{ $application->candidate->permanent_address }}</span>
        </div>
    </div>

    <div class="section-title">Academic Record</div>

    @php
        // Map each education entry to the closest matching row category.
        // Anything that doesn't match a known keyword falls under "Others".
        $categories = [
            'Matric/O Level' => null,
            'Intermediate/A Level' => null,
            "Bachelor's" => null,
            "Master's" => null,
            'Others' => null,
        ];

        foreach ($application->candidate->education as $edu) {
            $degreeLower = strtolower($edu->degree);
            if (str_contains($degreeLower, 'matric') || str_contains($degreeLower, 'o level') || str_contains($degreeLower, 'o-level')) {
                $key = 'Matric/O Level';
            } elseif (str_contains($degreeLower, 'intermediate') || str_contains($degreeLower, 'f.sc') || str_contains($degreeLower, 'fsc') || str_contains($degreeLower, 'a level') || str_contains($degreeLower, 'a-level') || str_contains($degreeLower, 'hssc')) {
                $key = 'Intermediate/A Level';
            } elseif (str_contains($degreeLower, 'bachelor') || str_contains($degreeLower, 'bs ') || str_starts_with($degreeLower, 'bs')) {
                $key = "Bachelor's";
            } elseif (str_contains($degreeLower, 'master') || str_contains($degreeLower, 'ms ') || str_starts_with($degreeLower, 'ms')) {
                $key = "Master's";
            } else {
                $key = 'Others';
            }
            $categories[$key] = $edu; // last matching entry wins if there are duplicates
        }
    @endphp

    <div class="academic-box">
        <table class="academic-table">
            <thead>
                <tr>
                    <th>Degree</th>
                    <th>Board, University</th>
                    <th>Major Subjects</th>
                    <th>Passing Year</th>
                    <th>Total Score</th>
                    <th>Obtained Score</th>
                    <th>Percentage/Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $label => $edu)
                <tr>
                    <td><strong>{{ $label }}</strong></td>
                    <td>{{ $edu->boardUniversity->title ?? '' }}</td>
                    <td>{{ $edu ? $edu->majorSubjects->pluck('title')->join(', ') : '' }}</td>
                    <td>{{ $edu ? \Carbon\Carbon::parse($edu->date_of_passing)->format('Y') : '' }}</td>
                    <td>{{ $edu->total_marks ?? '' }}</td>
                    <td>{{ $edu->obtained_marks ?? '' }}</td>
                    <td>
                        @if($edu && $edu->total_marks > 0)
                            {{ round(($edu->obtained_marks / $edu->total_marks) * 100, 1) }}%
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="print-btn-wrapper">
        <button class="print-btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

</body>
</html>