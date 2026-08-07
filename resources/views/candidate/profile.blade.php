@extends('admin.layout')

@section('title', 'Profile')

@push('styles')
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
    .form-group select,
    .form-group textarea {
        width: 100%;
        height: 46px;
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-size: 0.88rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .form-group textarea {
        height: auto;
        resize: vertical;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--primary);
    }

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
        background-color: var(--primary);
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

    /* ---------- Degree Programs (radio grid) ---------- */
    .program-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px 24px;
    }

    @media (max-width: 700px) {
        .program-grid {
            grid-template-columns: 1fr;
        }
    }

    .program-item {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 10px 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .program-item:hover {
        border-color: var(--primary);
    }

    .program-item input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
        cursor: pointer;
        flex-shrink: 0;
    }

    .program-item label {
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
        cursor: pointer;
    }

    .program-item.checked {
        border-color: var(--primary);
        background-color: var(--bg-soft);
    }

    /* ---------- Documents Checklist ---------- */
    .doc-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px 24px;
    }

    @media (max-width: 900px) {
        .doc-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .doc-grid {
            grid-template-columns: 1fr;
        }
    }

    .doc-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 0;
    }

    .doc-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
        cursor: pointer;
        flex-shrink: 0;
    }

    .doc-item label {
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
        cursor: pointer;
    }

    .form-note {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 14px;
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
    $selectedProgram = old('degree_program', $fallback('degree_program'));
    $selectedDocs = old('documents', $candidate->documents ?? []);
@endphp

<form method="POST" action="{{ route('profile.update') }}" id="profileForm" novalidate>
    @csrf

    {{-- Form No (system generated, read-only) --}}
    <div class="form-section">
        <div class="row">
            <div class="col-md-4 form-group">
                <label>Form No.</label>
                <input type="text" value="{{ $candidate->form_no ?? 'Auto Generated' }}" disabled>
            </div>
        </div>
    </div>

    {{-- Degree Programs --}}
    <div class="form-section">
        <h6>Application for Admission to Degree Programs</h6>
        <div class="program-grid">
            @php
                $programs = [
                    'BSCS (General Computing)',
                    'BSCS (Artificial Intelligence)',
                    'BSCS (Software Engineering)',
                    'BET (Information)',
                    'BET (Cyber)',
                    'BS MLT',
                    'ADP (AI)',
                    'ADP (Web Dev)',
                    'ADP (Mobile App Dev)',
                    'BS Biochemistry',
                    'BS Biotechnology',
                    'BS Microbiology',
                ];
            @endphp
            @foreach($programs as $i => $program)
            <div class="program-item {{ $selectedProgram === $program ? 'checked' : '' }}">
                <input type="radio" name="degree_program" id="program_{{ $i }}" value="{{ $program }}" {{ $selectedProgram === $program ? 'checked' : '' }} required>
                <label for="program_{{ $i }}">{{ $program }}</label>
            </div>
            @endforeach
        </div>
        @error('degree_program')
            <span class="field-error" style="display:block; margin-top:10px;">{{ $message }}</span>
        @enderror
    </div>

    {{-- Personal Information --}}
    <div class="form-section">
        <h6>Personal Information</h6>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Applicant's Name (Capital Letters)</label>
                <input type="text" name="applicant_name" class="only-alpha uppercase-input" maxlength="150" value="{{ old('applicant_name', $fallback('applicant_name')) }}" required>
                @error('applicant_name')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 form-group">
                <label>Applicant's Contact No</label>
                <input type="text" name="mobile_no" class="mobile-input" placeholder="03XXXXXXXXX" maxlength="11" inputmode="numeric" value="{{ old('mobile_no', $fallback('mobile_no')) }}" required>
                @error('mobile_no')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6 form-group">
                <label>CNIC</label>
                <input type="text" name="cnic" class="cnic-input" placeholder="xxxxx-xxxxxxx-x" maxlength="15" inputmode="numeric" value="{{ old('cnic', $fallback('cnic')) }}" required>
                @error('cnic')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 form-group">
                <label>E-Mail</label>
                <input type="email" name="email" class="email-input" maxlength="150" value="{{ old('email', $fallback('email')) }}" required>
                @error('email')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label>Sex</label>
                <select name="gender" required>
                    <option value="">Select</option>
                    @foreach(['Male', 'Female'] as $g)
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
                <label>PMAS-AAUR Registration No. <small style="font-weight:400;color:var(--text-muted);">(if previously enrolled)</small></label>
                <input type="text" name="pmas_registration_no" maxlength="100" value="{{ old('pmas_registration_no', $fallback('pmas_registration_no')) }}">
                @error('pmas_registration_no')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label>Applicant's Date of Birth</label>
                <input type="date" name="dob" value="{{ old('dob', $fallback('dob')) }}" required>
                @error('dob')
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

            <div class="col-md-12 form-group">
                <label>Any Medical Issue <small style="font-weight:400;color:var(--text-muted);">(if any)</small></label>
                <input type="text" name="medical_issue" maxlength="255" value="{{ old('medical_issue', $fallback('medical_issue')) }}">
                @error('medical_issue')
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
                <label>Sports & Co-curricular Activities <small style="font-weight:400;color:var(--text-muted);">(if any)</small></label>
                <textarea name="sports_activities" rows="2" maxlength="500">{{ old('sports_activities', $fallback('sports_activities')) }}</textarea>
                @error('sports_activities')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Father / Guardian Details --}}
    <div class="form-section">
        <h6>Father / Guardian Details</h6>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Father / Guardian Name</label>
                <input type="text" name="father_guardian_name" class="only-alpha" maxlength="150" value="{{ old('father_guardian_name', $candidate->father_guardian_name ?? '') }}" required>
                @error('father_guardian_name')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 form-group">
                <label>Father / Guardian CNIC</label>
                <input type="text" name="father_guardian_cnic" class="cnic-input" placeholder="xxxxx-xxxxxxx-x" maxlength="15" inputmode="numeric" value="{{ old('father_guardian_cnic', $candidate->father_guardian_cnic ?? '') }}" required>
                @error('father_guardian_cnic')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6 form-group">
                <label>Father / Guardian Contact No</label>
                <input type="text" name="father_guardian_mobile_no" class="mobile-input" placeholder="03XXXXXXXXX" maxlength="11" inputmode="numeric" value="{{ old('father_guardian_mobile_no', $candidate->father_guardian_mobile_no ?? '') }}" required>
                @error('father_guardian_mobile_no')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 form-group">
                <label>Relationship with Guardian</label>
                <input type="text" name="guardian_relationship" class="only-alpha" placeholder="e.g. Father, Uncle, Brother" maxlength="100" value="{{ old('guardian_relationship', $candidate->guardian_relationship ?? '') }}" required>
                @error('guardian_relationship')
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
                <input type="text" name="emergency_contact_name" class="only-alpha" maxlength="150" value="{{ old('emergency_contact_name', $candidate->emergency_contact_name ?? '') }}" required>
                @error('emergency_contact_name')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Relationship</label>
                <input type="text" name="emergency_contact_relationship" class="only-alpha" maxlength="100" value="{{ old('emergency_contact_relationship', $candidate->emergency_contact_relationship ?? '') }}" required>
                @error('emergency_contact_relationship')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Contact No</label>
                <input type="text" name="emergency_contact_no" class="mobile-input" placeholder="03XXXXXXXXX" maxlength="11" inputmode="numeric" value="{{ old('emergency_contact_no', $candidate->emergency_contact_no ?? '') }}" required>
                @error('emergency_contact_no')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Academic Record: Matriculation --}}
    <div class="form-section">
        <h6>Matriculation or Equivalent Examination</h6>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Name of Board</label>
                <input type="text" name="matric_board" class="only-alpha-num" maxlength="150" value="{{ old('matric_board', $candidate->matric_board ?? '') }}" required>
                @error('matric_board')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 form-group">
                <label>Passing Year</label>
                <input type="number" name="matric_passing_year" min="1990" max="{{ date('Y') }}" value="{{ old('matric_passing_year', $candidate->matric_passing_year ?? '') }}" required>
                @error('matric_passing_year')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label>Marks Obtained</label>
                <input type="number" step="0.01" min="0" name="matric_marks_obtained" value="{{ old('matric_marks_obtained', $candidate->matric_marks_obtained ?? '') }}" required>
                @error('matric_marks_obtained')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Total Marks</label>
                <input type="number" step="0.01" min="1" name="matric_total_marks" value="{{ old('matric_total_marks', $candidate->matric_total_marks ?? '') }}" required>
                @error('matric_total_marks')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Division / Grade</label>
                <input type="text" name="matric_division_grade" maxlength="20" value="{{ old('matric_division_grade', $candidate->matric_division_grade ?? '') }}" required>
                @error('matric_division_grade')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Academic Record: Intermediate --}}
    <div class="form-section">
        <h6>Intermediate or Equivalent Examination</h6>
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Major Subjects</label>
                <input type="text" name="inter_major_subjects" class="only-alpha-num-comma" placeholder="e.g. Physics, Chemistry, Mathematics" maxlength="255" value="{{ old('inter_major_subjects', $candidate->inter_major_subjects ?? '') }}" required>
                @error('inter_major_subjects')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6 form-group">
                <label>Name of Board</label>
                <input type="text" name="inter_board" class="only-alpha-num" maxlength="150" value="{{ old('inter_board', $candidate->inter_board ?? '') }}" required>
                @error('inter_board')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label>Roll No</label>
                <input type="text" name="inter_roll_no" maxlength="50" value="{{ old('inter_roll_no', $candidate->inter_roll_no ?? '') }}" required>
                @error('inter_roll_no')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Registration No</label>
                <input type="text" name="inter_registration_no" maxlength="50" value="{{ old('inter_registration_no', $candidate->inter_registration_no ?? '') }}" required>
                @error('inter_registration_no')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Passing Year</label>
                <input type="number" name="inter_passing_year" min="1990" max="{{ date('Y') }}" value="{{ old('inter_passing_year', $candidate->inter_passing_year ?? '') }}" required>
                @error('inter_passing_year')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label>Marks Obtained</label>
                <input type="number" step="0.01" min="0" name="inter_marks_obtained" value="{{ old('inter_marks_obtained', $candidate->inter_marks_obtained ?? '') }}" required>
                @error('inter_marks_obtained')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Total Marks</label>
                <input type="number" step="0.01" min="1" name="inter_total_marks" value="{{ old('inter_total_marks', $candidate->inter_total_marks ?? '') }}" required>
                @error('inter_total_marks')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4 form-group">
                <label>Division / Grade</label>
                <input type="text" name="inter_division_grade" maxlength="20" value="{{ old('inter_division_grade', $candidate->inter_division_grade ?? '') }}" required>
                @error('inter_division_grade')
                    <span class="field-error" style="display:block;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Documents Check List --}}
    <div class="form-section">
        <h6>Documents Check List</h6>
        <div class="doc-grid">
            @php
                $documents = [
                    'matric_results' => 'Matric Results',
                    'matric_character_certificate' => 'Matric Character Certificate',
                    'bform_cnic_applicant' => 'B-Form / CNIC (Applicant)',
                    'intermediate_result' => 'Intermediate Result',
                    'intermediate_character_certificate' => 'Intermediate Character Certificate',
                    'cnic_father_guardian' => 'CNIC Father / Guardian',
                    'three_latest_pictures' => 'Three Latest Pictures (Passport Size)',
                ];
            @endphp
            @foreach($documents as $key => $label)
            <div class="doc-item">
                <input type="checkbox" name="documents[]" id="doc_{{ $key }}" value="{{ $key }}"
                    {{ in_array($key, (array) $selectedDocs) ? 'checked' : '' }}>
                <label for="doc_{{ $key }}">{{ $label }}</label>
            </div>
            @endforeach
        </div>
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

        if (el.matches('.only-alpha')) {
            el.value = el.value.replace(/[^a-zA-Z\s.']/g, '');
        }

        if (el.matches('.only-alpha-num')) {
            el.value = el.value.replace(/[^a-zA-Z0-9\s.\-\/]/g, '');
        }

        if (el.matches('.only-alpha-num-comma')) {
            el.value = el.value.replace(/[^a-zA-Z0-9\s,.\-\/]/g, '');
        }

        if (el.matches('.uppercase-input')) {
            el.value = el.value.toUpperCase();
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

    // Highlight selected degree program card
    document.querySelectorAll('input[name="degree_program"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.program-item').forEach(function (item) {
                item.classList.remove('checked');
            });
            this.closest('.program-item').classList.add('checked');
        });
    });
</script>
@endpush