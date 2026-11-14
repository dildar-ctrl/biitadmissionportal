@extends('layouts.app')

@section('title', 'Profile')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">

<style>
    .form-section {
        background-color: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .form-section h6 {
        color: var(--primary);
        font-weight: 800;
        font-size: 1rem;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        height: 46px;
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-size: 0.88rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: var(--primary);
    }

    /* ===================== DROPDOWNS: responsive on all devices ===================== */
    .form-group select {
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        background-color: #fff;
    }

    .form-group select option {
        white-space: normal;
        word-break: break-word;
    }

    /* Grid columns ke andar select bahar na nikle */
    .form-group,
    .form-section .row > [class*="col-"] {
        min-width: 0;
    }

    /*
       Dropdown list (popup) ko page ke andar render karta hai, isliye screen se
       bahar nahi jata (Chrome 135+). Purane browsers mein normal native select chalega.
    */
    @supports (appearance: base-select) {
        .form-group select,
        .form-group select::picker(select) {
            appearance: base-select;
        }

        .form-group select {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            cursor: pointer;
        }

        .form-group select::picker-icon {
            flex-shrink: 0;
            transition: rotate 0.2s ease;
        }

        .form-group select:open::picker-icon {
            rotate: 180deg;
        }

        .form-group select::picker(select) {
            box-sizing: border-box;
            width: anchor-size(width);
            min-width: 0;
            max-width: calc(100vw - 24px);
            max-height: min(50vh, 320px);
            margin-top: 4px;
            padding: 4px;
            background-color: #fff;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            overflow-x: hidden;
            overflow-y: auto;
        }

        .form-group select option {
            box-sizing: border-box;
            width: 100%;
            padding: 10px 12px;
            font-size: 0.88rem;
            border-radius: 6px;
            cursor: pointer;
        }

        .form-group select option:hover,
        .form-group select option:focus {
            background-color: var(--bg-soft);
        }

        .form-group select option:checked {
            font-weight: 700;
        }
    }
    /* ===================== /DROPDOWNS ===================== */

    .form-group .field-error {
        color: #c0392b;
        font-size: 0.76rem;
        font-weight: 600;
        margin-top: 4px;
        display: none;
    }

    .form-group input.field-invalid {
        border-color: #c0392b;
    }

    .form-group input:disabled {
        background-color: #f5f5f5;
        cursor: not-allowed;
    }

    .save-btn {
       background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
        color: #fff;
        border: none;
        padding: 13px 34px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
    }

    .save-btn:hover {
        background-color: var(--primary-dark);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 8px;
    }

    .cancel-btn {
        background-color: #fff;
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        padding: 13px 30px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .cancel-btn:hover {
        background-color: var(--bg-soft);
        color: var(--text-dark);
    }

    .education-block {
        border: 1.5px dashed var(--border);
        border-radius: 10px;
        padding: 18px 18px 4px;
        margin-bottom: 18px;
        position: relative;
    }

    .education-block .edu-index-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 10px;
    }

    .remove-edu-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #fdecea;
        color: #c0392b;
        border: none;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        display: none;
    }

    .add-edu-btn {
        background-color: #fff;
        color: var(--primary);
        border: 1.5px dashed var(--primary);
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
        margin-top: 4px;
    }

    .add-edu-btn:hover {
        background-color: var(--primary);
        color: #fff;
    }
</style>
@endpush

@section('content')

<div class="stepper-title">Profile — Create / Update</div>

@php
    $signup = $candidateLogin ?? null;
    $fallback = function ($field) use ($candidate, $signup) {
        if (isset($candidate) && $candidate && filled($candidate->{$field} ?? null)) {
            return $candidate->{$field};
        }
        if ($signup && filled($signup->{$field} ?? null)) {
            return $signup->{$field};
        }
        return '';
    };
@endphp

<form method="POST" action="{{ route('profile.update') }}" id="profileForm" novalidate>
    @csrf

    {{-- Personal Information --}}
    <div class="form-section">
        <h6>Personal Information</h6>
        <div class="row">
            <div class="col-md-4 form-group">
                <label>First Name</label>
                <input type="text" name="fname" class="only-alpha" maxlength="100" value="{{ old('fname', $fallback('fname')) }}" required>
                @error('fname')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Last Name</label>
                <input type="text" name="lname" class="only-alpha" maxlength="100" value="{{ old('lname', $fallback('lname')) }}" required>
                @error('lname')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Email Address</label>
                <input type="email" name="email" maxlength="150" value="{{ old('email', $fallback('email')) }}" required>
                @error('email')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label>CNIC</label>
                <input type="text" name="cnic" class="cnic-input" placeholder="xxxxx-xxxxxxx-x" maxlength="15" inputmode="numeric" value="{{ old('cnic', $fallback('cnic')) }}" required>
                @error('cnic')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="{{ old('dob', $fallback('dob')) }}" required>
                @error('dob')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Gender</label>
                <select name="gender" required>
                    <option value="">Select</option>
                    @foreach(['Male', 'Female', 'Other'] as $g)
                    <option value="{{ $g }}" {{ old('gender', $fallback('gender')) == $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
                @error('gender')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label>Marital Status</label>
                <select name="marital_status" required>
                    <option value="">Select</option>
                    @foreach(['Single', 'Married'] as $m)
                    <option value="{{ $m }}" {{ old('marital_status', $fallback('marital_status')) == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
                @error('marital_status')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Mobile Number</label>
                <input type="text" name="mobile_no" class="mobile-input" placeholder="03XXXXXXXXX" maxlength="11" inputmode="numeric" value="{{ old('mobile_no', $fallback('mobile_no')) }}" required>
                @error('mobile_no')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Religion</label>
                <input type="text" name="religion" class="only-alpha" maxlength="100" value="{{ old('religion', $fallback('religion')) }}" required>
                @error('religion')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label>Nationality</label>
                <input type="text" name="nationality" class="only-alpha" maxlength="100" value="{{ old('nationality', $fallback('nationality')) }}" required>
                @error('nationality')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Any Medical Issue <small style="font-weight:400;color:var(--text-muted);">(write "None" if not applicable)</small></label>
                <input type="text" name="medicalissue" maxlength="100" value="{{ old('medicalissue', $fallback('medicalissue')) }}" required>
                @error('medicalissue')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>PMAS-AAUR Registration No. <small style="font-weight:400;color:var(--text-muted);">(if previously enrolled)</small></label>
                <input type="text" name="PMASREGNO" maxlength="100" value="{{ old('PMASREGNO', $fallback('PMASREGNO')) }}">
                @error('PMASREGNO')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6 form-group">
                <label>Postal Address</label>
                <input type="text" name="postal_address" maxlength="255" value="{{ old('postal_address', $fallback('postal_address')) }}" required>
                @error('postal_address')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 form-group">
                <label>Permanent Address</label>
                <input type="text" name="permanent_address" maxlength="255" value="{{ old('permanent_address', $fallback('permanent_address')) }}" required>
                @error('permanent_address')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-12 form-group">
                <label>Sports &amp; Co-curricular Activities <small style="font-weight:400;color:var(--text-muted);">(if any)</small></label>
                <input type="text" name="activities" maxlength="100" value="{{ old('activities', $fallback('activities')) }}">
                @error('activities')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Father / Guardian Details --}}
    <div class="form-section">
        <h6>Father / Guardian Details</h6>
        <div class="row">
            <div class="col-md-4 form-group">
                <label>Father / Guardian Name</label>
                <input type="text" name="father_name" class="only-alpha" maxlength="150" value="{{ old('father_name', $candidate->father_name ?? '') }}" required>
                @error('father_name')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Father / Guardian CNIC</label>
                <input type="text" name="father_cnic" class="cnic-input" placeholder="xxxxx-xxxxxxx-x" maxlength="15" inputmode="numeric" value="{{ old('father_cnic', $candidate->father_cnic ?? '') }}" required>
                @error('father_cnic')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Father / Guardian Contact No</label>
                <input type="text" name="father_mobile_no" class="mobile-input" placeholder="03XXXXXXXXX" maxlength="11" inputmode="numeric" value="{{ old('father_mobile_no', $candidate->father_mobile_no ?? '') }}" required>
                @error('father_mobile_no')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label>Relationship with Guardian</label>
                <input type="text" name="relationshipwithguardian" class="only-alpha" placeholder="e.g. Father" maxlength="150" value="{{ old('relationshipwithguardian', $candidate->relationshipwithguardian ?? 'Father') }}" required>
                @error('relationshipwithguardian')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Emergency Contact --}}
    <div class="form-section">
        <h6>Emergency Contact</h6>
        <div class="row">
            <div class="col-md-4 form-group">
                <label>Emergency Contact Name</label>
                <input type="text" name="emergencycontactname" class="only-alpha" maxlength="150" value="{{ old('emergencycontactname', $candidate->emergencycontactname ?? '') }}" required>
                @error('emergencycontactname')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Relationship</label>
                <input type="text" name="emergencycontactrelationship" class="only-alpha" maxlength="100" value="{{ old('emergencycontactrelationship', $candidate->emergencycontactrelationship ?? '') }}" required>
                @error('emergencycontactrelationship')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Contact No</label>
                <input type="text" name="emergencycontact" class="mobile-input" placeholder="03XXXXXXXXX" maxlength="11" inputmode="numeric" value="{{ old('emergencycontact', $candidate->emergencycontact ?? '') }}" required>
                @error('emergencycontact')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Educational Qualification --}}
<div class="form-section">
    <h6>Educational Qualification</h6>

    @php
        $eduRows = old('education', isset($educations) && count($educations) ? $educations->toArray() : []);
        $matricData = $eduRows[0] ?? [];
        $interData = $eduRows[1] ?? [];
        $extraRows = collect($eduRows)->slice(2)->values();
    @endphp

    @if(!old('education') && (!isset($educations) || count($educations) === 0))
    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 14px;">
        <i class="fas fa-circle-info me-1"></i>
        Matric/O-Level and Intermediate/A-Level are required.
    </p>
    @endif

    <div id="education-wrapper">

        {{-- ===================== MATRIC / O-LEVEL (independent block) ===================== --}}
        <div class="education-block" data-index="0">
            <div class="edu-index-label">Matric / O-Level</div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Degree</label>
                    <input type="text" name="education[0][degree]" class="only-alpha-num" placeholder="e.g. Matric / O-Level" maxlength="150" value="{{ old('education.0.degree', $matricData['degree'] ?? '') }}" required>
                    @error('education.0.degree')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label>Name of Board / University</label>
                    <input type="text" name="education[0][boardname]" class="only-alpha-num" maxlength="255" value="{{ old('education.0.boardname', $matricData['boardname'] ?? '') }}" required>
                    @error('education.0.boardname')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12 form-group">
                    <label>Major Subject(s) <small style="font-weight:400;color:var(--text-muted);">(e.g. Physics, Chemistry, Mathematics)</small></label>
                    <input type="text" name="education[0][majorsubjects]" class="only-alpha-num-comma" maxlength="255" placeholder="e.g. Physics, Chemistry, Mathematics" value="{{ old('education.0.majorsubjects', $matricData['majorsubjects'] ?? '') }}">
                    @error('education.0.majorsubjects')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label>Date of Passing</label>
                    <input type="date" name="education[0][date_of_passing]" value="{{ old('education.0.date_of_passing', $matricData['date_of_passing'] ?? '') }}" required>
                    @error('education.0.date_of_passing')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label>Roll No / Registration No</label>
                    <input type="text" name="education[0][registrationno]" maxlength="50" value="{{ old('education.0.registrationno', $matricData['registrationno'] ?? '') }}">
                    @error('education.0.registrationno')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label>Division / Grade</label>
                    <input type="text" name="education[0][division]" maxlength="50" value="{{ old('education.0.division', $matricData['division'] ?? '') }}">
                    @error('education.0.division')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-2 form-group">
                    <label>Marks Obtained</label>
                    <input type="number" step="0.01" min="0" name="education[0][obtained_marks]" value="{{ old('education.0.obtained_marks', $matricData['obtained_marks'] ?? '') }}" required>
                    @error('education.0.obtained_marks')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-2 form-group">
                    <label>Total Marks</label>
                    <input type="number" step="0.01" min="1" name="education[0][total_marks]" value="{{ old('education.0.total_marks', $matricData['total_marks'] ?? '') }}" required>
                    @error('education.0.total_marks')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ===================== INTERMEDIATE / A-LEVEL (independent block) ===================== --}}
        <div class="education-block" data-index="1">
            <div class="edu-index-label">Intermediate / A-Level</div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Degree</label>
                    <input type="text" name="education[1][degree]" class="only-alpha-num" placeholder="e.g. Intermediate / A-Level" maxlength="150" value="{{ old('education.1.degree', $interData['degree'] ?? '') }}" required>
                    @error('education.1.degree')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label>Name of Board / University</label>
                    <input type="text" name="education[1][boardname]" class="only-alpha-num" maxlength="255" value="{{ old('education.1.boardname', $interData['boardname'] ?? '') }}" required>
                    @error('education.1.boardname')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12 form-group">
                    <label>Major Subject(s) <small style="font-weight:400;color:var(--text-muted);">(e.g. Physics, Chemistry, Mathematics)</small></label>
                    <input type="text" name="education[1][majorsubjects]" class="only-alpha-num-comma" maxlength="255" placeholder="e.g. Physics, Chemistry, Mathematics" value="{{ old('education.1.majorsubjects', $interData['majorsubjects'] ?? '') }}">
                    @error('education.1.majorsubjects')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label>Date of Passing</label>
                    <input type="date" name="education[1][date_of_passing]" value="{{ old('education.1.date_of_passing', $interData['date_of_passing'] ?? '') }}" required>
                    @error('education.1.date_of_passing')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label>Roll No / Registration No</label>
                    <input type="text" name="education[1][registrationno]" maxlength="50" value="{{ old('education.1.registrationno', $interData['registrationno'] ?? '') }}">
                    @error('education.1.registrationno')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label>Division / Grade</label>
                    <input type="text" name="education[1][division]" maxlength="50" value="{{ old('education.1.division', $interData['division'] ?? '') }}">
                    @error('education.1.division')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-2 form-group">
                    <label>Marks Obtained</label>
                    <input type="number" step="0.01" min="0" name="education[1][obtained_marks]" value="{{ old('education.1.obtained_marks', $interData['obtained_marks'] ?? '') }}" required>
                    @error('education.1.obtained_marks')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-2 form-group">
                    <label>Total Marks</label>
                    <input type="number" step="0.01" min="1" name="education[1][total_marks]" value="{{ old('education.1.total_marks', $interData['total_marks'] ?? '') }}" required>
                    @error('education.1.total_marks')
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ===================== ADDITIONAL DEGREES (loop, dynamic add/remove) ===================== --}}
        @foreach($extraRows as $j => $edu)
        @php $i = $j + 2; @endphp
        <div class="education-block" data-index="{{ $i }}">
            <div class="edu-index-label">Degree #{{ $i - 1 }}</div>

            <button type="button" class="remove-edu-btn" style="display: inline-block;" onclick="removeEducationBlock(this)">Remove</button>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Degree</label>
                    <input type="text" name="education[{{ $i }}][degree]" class="only-alpha-num" placeholder="e.g. Bachelor / Master" maxlength="150" value="{{ old("education.$i.degree", $edu['degree'] ?? '') }}" required>
                    @error("education.$i.degree")
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label>Name of Board / University</label>
                    <input type="text" name="education[{{ $i }}][boardname]" class="only-alpha-num" maxlength="255" value="{{ old("education.$i.boardname", $edu['boardname'] ?? '') }}" required>
                    @error("education.$i.boardname")
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-12 form-group">
                    <label>Major Subject(s) <small style="font-weight:400;color:var(--text-muted);">(e.g. Physics, Chemistry, Mathematics)</small></label>
                    <input type="text" name="education[{{ $i }}][majorsubjects]" class="only-alpha-num-comma" maxlength="255" placeholder="e.g. Physics, Chemistry, Mathematics" value="{{ old("education.$i.majorsubjects", $edu['majorsubjects'] ?? '') }}">
                    @error("education.$i.majorsubjects")
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-3 form-group">
                    <label>Date of Passing</label>
                    <input type="date" name="education[{{ $i }}][date_of_passing]" value="{{ old("education.$i.date_of_passing", $edu['date_of_passing'] ?? '') }}" required>
                    @error("education.$i.date_of_passing")
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label>Roll No / Registration No</label>
                    <input type="text" name="education[{{ $i }}][registrationno]" maxlength="50" value="{{ old("education.$i.registrationno", $edu['registrationno'] ?? '') }}">
                    @error("education.$i.registrationno")
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3 form-group">
                    <label>Division / Grade</label>
                    <input type="text" name="education[{{ $i }}][division]" maxlength="50" value="{{ old("education.$i.division", $edu['division'] ?? '') }}">
                    @error("education.$i.division")
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-2 form-group">
                    <label>Marks Obtained</label>
                    <input type="number" step="0.01" min="0" name="education[{{ $i }}][obtained_marks]" value="{{ old("education.$i.obtained_marks", $edu['obtained_marks'] ?? '') }}" required>
                    @error("education.$i.obtained_marks")
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-2 form-group">
                    <label>Total Marks</label>
                    <input type="number" step="0.01" min="1" name="education[{{ $i }}][total_marks]" value="{{ old("education.$i.total_marks", $edu['total_marks'] ?? '') }}" required>
                    @error("education.$i.total_marks")
                        <span class="field-error" style="display:block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- <button type="button" class="add-edu-btn" onclick="addEducationBlock()">
        <i class="fas fa-plus me-1"></i> Add More
    </button> -->
</div>
    <div class="form-actions">
        <a href="{{ route('dashboard') }}" class="cancel-btn">Cancel</a>
        <button type="submit" class="save-btn"><i class="fas fa-save me-2"></i>Save & Continue</button>
    </div>

</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('input', function (e) {
        const el = e.target;

        // Only letters, spaces, dot and apostrophe
        if (el.matches('.only-alpha')) {
            el.value = el.value.replace(/[^a-zA-Z\s.']/g, '');
        }

        // Letters, numbers, spaces, dots, dashes, slashes (for degree, board name)
        if (el.matches('.only-alpha-num')) {
            el.value = el.value.replace(/[^a-zA-Z0-9\s.\-\/]/g, '');
        }

        // Letters, numbers, spaces, commas, dots, dashes, slashes (for major subjects)
        if (el.matches('.only-alpha-num-comma')) {
            el.value = el.value.replace(/[^a-zA-Z0-9\s,.\-\/]/g, '');
        }

        if (el.matches('.cnic-input')) {
            let digits = el.value.replace(/[^0-9]/g, '').slice(0, 13);
            let formatted = digits.slice(0, 5);
            if (digits.length > 5) formatted += '-' + digits.slice(5, 12);
            if (digits.length > 12) formatted += '-' + digits.slice(12, 13);
            el.value = formatted;
        }

        if (el.matches('.mobile-input')) {
            el.value = el.value.replace(/[^0-9]/g, '').slice(0, 11);
        }

        if (el.matches('.email-input')) {
            el.value = el.value.replace(/[^a-zA-Z0-9@._\-]/g, '');
        }
    });

    window.eduIndex = Math.max(document.querySelectorAll('.education-block').length, 2);

    function addEducationBlock() {
    const wrapper = document.getElementById('education-wrapper');
    const blocks = wrapper.querySelectorAll('.education-block');
    const templateBlock = blocks[blocks.length - 1];
    const newBlock = templateBlock.cloneNode(true);

        newBlock.dataset.index = eduIndex;
        newBlock.querySelector('.edu-index-label').textContent = 'Degree #' + (eduIndex - 1);

        newBlock.querySelectorAll('input, select').forEach(function (field) {
            field.name = field.name.replace(/education\[\d+\]/, 'education[' + eduIndex + ']');
            if (field.tagName === 'SELECT') {
                field.selectedIndex = 0;
            } else if (field.type === 'checkbox') {
                field.checked = false;
            } else {
                field.value = '';
            }
        });

        const removeBtn = newBlock.querySelector('.remove-edu-btn');
        removeBtn.style.display = 'inline-block';

        wrapper.appendChild(newBlock);
        eduIndex++;
    }

    function removeEducationBlock(btn) {
        const block = btn.closest('.education-block');
        block.remove();
    }
</script>
@endpush