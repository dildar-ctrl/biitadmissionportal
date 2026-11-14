@extends('layouts.app')

@section('title', 'Documents')
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

    .file-upload-box {
        border: 2px dashed var(--border);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .file-upload-box:hover {
        border-color: var(--primary);
        background-color: #f8f9fa;
    }

    .file-upload-box input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-upload-box .upload-icon {
        font-size: 2rem;
        color: var(--primary);
        margin-bottom: 8px;
    }

    .file-upload-box.has-file {
        border-color: #27ae60;
        background-color: #f0fff4;
    }

    /* Highlight the box in red when the server rejected this field */
    .file-upload-box.has-error {
        border-color: #c0392b;
        background-color: #fdecea;
    }

    .file-upload-box .file-name {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-top: 5px;
        word-break: break-all;
    }

    .file-upload-box .file-preview {
        margin-top: 8px;
    }

    .file-upload-box .file-preview img {
        max-height: 80px;
        max-width: 80px;
        border-radius: 8px;
    }

    /* Keeps the "view uploaded file" link clickable above the invisible file input */
    .file-upload-box .file-preview a {
        position: relative;
        z-index: 2;
        color: var(--primary);
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* "I do not have a CNIC" option: small single-line pill, checkbox next to its text */
    .cnic-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        margin-bottom: 14px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        background-color: #f8faf9;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-dark);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cnic-toggle:hover {
        border-color: var(--primary);
    }

    .cnic-toggle.active {
        border-color: #27ae60;
        background-color: #f0fff4;
    }

    .cnic-toggle input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin: 0;
        flex-shrink: 0;
        cursor: pointer;
        accent-color: #27ae60;
    }

    .cnic-toggle .toggle-hint {
        font-weight: 400;
        color: var(--text-muted);
    }

    .required-star {
        color: #c0392b;
    }

    .document-status {
        font-size: 0.75rem;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 5px;
    }

    .document-status.uploaded {
        background: #d4edda;
        color: #155724;
    }

    /* Field-specific error message shown directly under each upload box */
    .field-error {
        color: #c0392b;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 6px;
        display: block;
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

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #f5c6cb;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }
</style>
@endpush

@section('content')

<div class="stepper-title">Documents — Upload</div>

{{-- success/error flash messages are now shown globally by layouts/app.blade.php --}}

{{-- Consolidated summary of every validation error, shown at the top of the page --}}
@if($errors->any())
<div class="alert-danger" style="padding:15px;border-radius:8px;margin-bottom:20px;">
    <strong>Please fix the following errors:</strong>
    <ul style="margin-top:10px;padding-left:20px;margin-bottom:0;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('document.store') }}" enctype="multipart/form-data" id="documentForm">
    @csrf

    {{-- ===================== Passport Size Photo ===================== --}}
    <div class="form-section">
        <h6>📷 Passport Size Photo <span class="required-star">*</span></h6>
        <div class="row">
            <div class="col-md-6">
                {{-- box gets a red outline if the server rejected this specific file --}}
                <div class="file-upload-box {{ $errors->has('photo') ? 'has-error' : '' }}" id="photo-box">
                    <div class="upload-icon">📷</div>
                    <p><strong>Upload Photo</strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">
                        Square photo (1:1 ratio) • Minimum 150×150 pixels • Blue Background<br>
                        PNG, JPG, JPEG • Max 5MB
                    </p>
                    <input type="file" name="photo" accept=".png,.jpg,.jpeg"
                           data-max-size="5242880"
                           data-preview-id="photo-preview"
                           onchange="validateFileSize(this)" required>
                    <div id="photo-preview" class="file-preview"></div>
                </div>

                {{-- field-specific error message for this exact input --}}
                @error('photo')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror

            </div>
        </div>
    </div>

    {{-- ===================== CNIC / B-Form ===================== --}}
    @php
        // Ticked when the form was re-posted with the box ticked; otherwise
        // when a B-Form is already stored for this candidate.
        $noCnic = session()->hasOldInput() ? (bool) old('nocnic') : isset($documents[9]);
    @endphp
    <div class="form-section">
        <h6>🪪 CNIC / B-Form <span class="required-star">*</span></h6>

        {{-- Compact single-line option: checkbox sits right next to its text --}}
        <label class="cnic-toggle {{ $noCnic ? 'active' : '' }}" for="nocnic" id="cnic-toggle">
            <input type="checkbox" name="nocnic" value="1" id="nocnic" {{ $noCnic ? 'checked' : '' }}>
            <span>I do not have a CNIC <span class="toggle-hint">(upload B-Form instead)</span></span>
        </label>

        {{-- CNIC front + back (shown when the candidate has a CNIC) --}}
        <div class="row" id="cnic-group" style="{{ $noCnic ? 'display:none;' : '' }}">
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('cnicfront') ? 'has-error' : '' }}" id="cnicfront-box">
                    <div class="upload-icon">🪪</div>
                    <p><strong>CNIC Front Side</strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 5MB</p>
                    <input type="file" name="cnicfront" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="5242880"
                           data-preview-id="cnicfront-preview"
                           onchange="validateFileSize(this)" required>
                    <div id="cnicfront-preview" class="file-preview"></div>
                </div>
                @error('cnicfront')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('cnicback') ? 'has-error' : '' }}" id="cnicback-box">
                    <div class="upload-icon">🪪</div>
                    <p><strong>CNIC Back Side</strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 5MB</p>
                    <input type="file" name="cnicback" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="5242880"
                           data-preview-id="cnicback-preview"
                           onchange="validateFileSize(this)" required>
                    <div id="cnicback-preview" class="file-preview"></div>
                </div>
                @error('cnicback')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- B-Form, single image (shown when the candidate has no CNIC) --}}
        <div class="row" id="bform-group" style="{{ $noCnic ? '' : 'display:none;' }}">
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('bform') ? 'has-error' : '' }}" id="bform-box">
                    <div class="upload-icon">🪪</div>
                    <p><strong>B-Form</strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 5MB</p>
                    <input type="file" name="bform" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="5242880"
                           data-preview-id="bform-preview"
                           onchange="validateFileSize(this)">
                    <div id="bform-preview" class="file-preview"></div>
                </div>
                @error('bform')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ===================== CNIC Father/Guardian ===================== --}}
    <div class="form-section">
        <h6>🪪 CNIC Father/Guardian <span class="required-star">*</span></h6>
        <div class="row">
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('fathercnicfront') ? 'has-error' : '' }}" id="fathercnicfront-box">
                    <div class="upload-icon">🪪</div>
                    <p><strong>Father/Guardian CNIC Front Side</strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 5MB</p>
                    <input type="file" name="fathercnicfront" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="5242880"
                           data-preview-id="fathercnicfront-preview"
                           onchange="validateFileSize(this)" required>
                    <div id="fathercnicfront-preview" class="file-preview"></div>
                </div>
                @error('fathercnicfront')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('fathercnicback') ? 'has-error' : '' }}" id="fathercnicback-box">
                    <div class="upload-icon">🪪</div>
                    <p><strong>Father/Guardian CNIC Back Side</strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 5MB</p>
                    <input type="file" name="fathercnicback" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="5242880"
                           data-preview-id="fathercnicback-preview"
                           onchange="validateFileSize(this)" required>
                    <div id="fathercnicback-preview" class="file-preview"></div>
                </div>
                @error('fathercnicback')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

   {{-- ===================== Education Documents ===================== --}}
<div class="form-section">
    <h6>🎓 Education Documents <span class="required-star">*</span></h6>
    <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:15px;">
        Please upload a Result for every degree listed in your profile.
        <br>Allowed: PNG, JPG, JPEG, PDF • Max 10MB each
    </p>

    @php
        $matricEdu = $educations->first(function ($e) {
            $d = strtolower($e->degree ?? '');
            return str_contains($d, 'matric') || str_contains($d, 'o level');
        });

        $interEdu = $educations->first(function ($e) {
            $d = strtolower($e->degree ?? '');
            return !(str_contains($d, 'matric') || str_contains($d, 'o level'));
        });

        $extraEducations = $educations->reject(function ($e) use ($matricEdu, $interEdu) {
            return ($matricEdu && $e->id === $matricEdu->id) || ($interEdu && $e->id === $interEdu->id);
        });
    @endphp

    {{-- ===================== MATRIC (independent block) ===================== --}}
    @if($matricEdu)
    <div style="border-left:3px solid var(--primary);padding-left:15px;margin-bottom:20px;">
        <h6 style="color:var(--primary);font-weight:700;margin-bottom:15px;">📄 {{ $matricEdu->degree }} Documents</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('certificate_'.$matricEdu->id) ? 'has-error' : '' }}" id="certificate-{{ $matricEdu->id }}-box">
                    <div class="upload-icon">📄</div>
                    <p><strong>{{ $matricEdu->degree }} Result <span class="required-star">*</span></strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 10MB</p>
                    <input type="file" name="certificate_{{ $matricEdu->id }}" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="10485760"
                           data-preview-id="certificate-{{ $matricEdu->id }}-preview"
                           onchange="validateFileSize(this)" required>
                    <div id="certificate-{{ $matricEdu->id }}-preview" class="file-preview"></div>
                </div>
                @error('certificate_'.$matricEdu->id)
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('dmc_'.$matricEdu->id) ? 'has-error' : '' }}" id="dmc-{{ $matricEdu->id }}-box">
                    <div class="upload-icon">📄</div>
                    <p><strong>{{ $matricEdu->degree }} Character Certificate <span style="font-weight:400;color:var(--text-muted);">(Optional)</span></strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 10MB</p>
                    <input type="file" name="dmc_{{ $matricEdu->id }}" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="10485760"
                           data-preview-id="dmc-{{ $matricEdu->id }}-preview"
                           onchange="validateFileSize(this)">
                    <div id="dmc-{{ $matricEdu->id }}-preview" class="file-preview"></div>
                </div>
                @error('dmc_'.$matricEdu->id)
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    @endif

    {{-- ===================== INTERMEDIATE (independent block) ===================== --}}
    @if($interEdu)
    <div style="border-left:3px solid var(--primary);padding-left:15px;margin-bottom:20px;">
        <h6 style="color:var(--primary);font-weight:700;margin-bottom:15px;">📄 {{ $interEdu->degree }} Documents</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('certificate_'.$interEdu->id) ? 'has-error' : '' }}" id="certificate-{{ $interEdu->id }}-box">
                    <div class="upload-icon">📄</div>
                    <p><strong>{{ $interEdu->degree }} Result <span class="required-star">*</span></strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 10MB</p>
                    <input type="file" name="certificate_{{ $interEdu->id }}" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="10485760"
                           data-preview-id="certificate-{{ $interEdu->id }}-preview"
                           onchange="validateFileSize(this)" required>
                    <div id="certificate-{{ $interEdu->id }}-preview" class="file-preview"></div>
                </div>
                @error('certificate_'.$interEdu->id)
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('dmc_'.$interEdu->id) ? 'has-error' : '' }}" id="dmc-{{ $interEdu->id }}-box">
                    <div class="upload-icon">📄</div>
                    <p><strong>{{ $interEdu->degree }} Character Certificate <span style="font-weight:400;color:var(--text-muted);">(Optional)</span></strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 10MB</p>
                    <input type="file" name="dmc_{{ $interEdu->id }}" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="10485760"
                           data-preview-id="dmc-{{ $interEdu->id }}-preview"
                           onchange="validateFileSize(this)">
                    <div id="dmc-{{ $interEdu->id }}-preview" class="file-preview"></div>
                </div>
                @error('dmc_'.$interEdu->id)
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    @endif

    {{-- ===================== EXTRA DEGREES (loop stays dynamic) ===================== --}}
    @foreach($extraEducations as $edu)
    <div style="border-left:3px solid var(--primary);padding-left:15px;margin-bottom:20px;">
        <h6 style="color:var(--primary);font-weight:700;margin-bottom:15px;">📄 {{ $edu->degree }} Documents</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('certificate_'.$edu->id) ? 'has-error' : '' }}" id="certificate-{{ $edu->id }}-box">
                    <div class="upload-icon">📄</div>
                    <p><strong>{{ $edu->degree }} Result <span class="required-star">*</span></strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 10MB</p>
                    <input type="file" name="certificate_{{ $edu->id }}" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="10485760"
                           data-preview-id="certificate-{{ $edu->id }}-preview"
                           onchange="validateFileSize(this)" required>
                    <div id="certificate-{{ $edu->id }}-preview" class="file-preview"></div>
                </div>
                @error('certificate_'.$edu->id)
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('dmc_'.$edu->id) ? 'has-error' : '' }}" id="dmc-{{ $edu->id }}-box">
                    <div class="upload-icon">📄</div>
                    <p><strong>{{ $edu->degree }} Character Certificate <span style="font-weight:400;color:var(--text-muted);">(Optional)</span></strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">PNG, JPG, JPEG, PDF • Max 10MB</p>
                    <input type="file" name="dmc_{{ $edu->id }}" accept=".png,.jpg,.jpeg,.pdf"
                           data-max-size="10485760"
                           data-preview-id="dmc-{{ $edu->id }}-preview"
                           onchange="validateFileSize(this)">
                    <div id="dmc-{{ $edu->id }}-preview" class="file-preview"></div>
                </div>
                @error('dmc_'.$edu->id)
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    @endforeach
</div>


    {{-- Cancel goes back to Profile; Save & Continue submits the form --}}
    <div class="form-actions">
        <a href="{{ route('profile.edit') }}" class="cancel-btn">Back</a>
        <button type="submit" class="save-btn"><i class="fas fa-upload me-2"></i>Save & Continue</button>
    </div>

</form>

@endsection

@push('scripts')
<script>
// ============================================================
// CNIC / B-FORM TOGGLE
// Shows either the CNIC (front + back) fields or the single B-Form
// field. The hidden group's inputs are disabled and lose "required"
// so they are neither submitted nor block the form.
// A field that already has a stored file never gets "required".
// ============================================================
function setUploadGroupState(groupId, active) {
    const group = document.getElementById(groupId);
    if (!group) return;

    group.style.display = active ? '' : 'none';
    group.querySelectorAll('input[type="file"]').forEach(function (input) {
        input.disabled = !active;
        if (active && !input.dataset.hasExisting) {
            input.setAttribute('required', 'required');
        } else {
            input.removeAttribute('required');
        }
    });
}

function toggleCnicMode() {
    const noCnic = document.getElementById('nocnic').checked;
    setUploadGroupState('cnic-group', !noCnic);
    setUploadGroupState('bform-group', noCnic);

    // Highlight the option bar while it is ticked
    const bar = document.getElementById('cnic-toggle');
    if (bar) bar.classList.toggle('active', noCnic);
}

document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('nocnic');
    if (checkbox) {
        checkbox.addEventListener('change', toggleCnicMode);
        toggleCnicMode(); // apply the initial state
    }
});

// ============================================================
// CLIENT-SIDE FILE VALIDATION
// Runs the moment a file is chosen, before the form is even
// submitted — catches obviously wrong size/type early so the
// user doesn't have to wait for a server round-trip.
// Note: this is a convenience check only. The real security
// checks (content inspection, magic bytes, etc.) always run
// server-side in DocumentController, since client-side checks
// can be bypassed.
// ============================================================

function validateFileSize(input) {
    const file = input.files[0];
    if (!file) return true;

    const maxSize = parseInt(input.dataset.maxSize) || 5242880;
    const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);

    // Reject the file client-side if it's already over the limit
    if (file.size > maxSize) {
        alert('❌ File size is too large!\n\n' +
              'File: ' + file.name + '\n' +
              'Size: ' + fileSizeMB + 'MB\n' +
              'Maximum allowed: ' + maxSizeMB + 'MB\n\n' +
              'Please compress or choose a smaller file.');
        input.value = '';
        const box = input.closest('.file-upload-box');
        if (box) {
            box.classList.remove('has-file');
            const preview = box.querySelector('.file-preview');
            if (preview) preview.innerHTML = '';
        }
        return false;
    }

    // Quick extension check against the field's "accept" attribute
    const acceptAttr = input.getAttribute('accept');
    if (acceptAttr) {
        const allowedExtensions = acceptAttr.split(',').map(ext => ext.trim().replace('.', '').toLowerCase());
        const fileExt = file.name.split('.').pop().toLowerCase();

        if (allowedExtensions.length > 0 && allowedExtensions[0] !== '' && !allowedExtensions.includes(fileExt)) {
            alert('❌ Invalid file type!\n\n' +
                  'File: ' + file.name + '\n' +
                  'Allowed: ' + allowedExtensions.join(', ') + '\n\n' +
                  'Please upload a valid file.');
            input.value = '';
            const box = input.closest('.file-upload-box');
            if (box) {
                box.classList.remove('has-file');
                const preview = box.querySelector('.file-preview');
                if (preview) preview.innerHTML = '';
            }
            return false;
        }
    }

    // File looks fine so far — show a quick preview
    const previewId = input.dataset.previewId;
    if (previewId) {
        previewFile(input, previewId);
    }

    return true;
}

// Shows a thumbnail for images, or just the filename for PDFs
function previewFile(input, previewId) {
    const preview = document.getElementById(previewId);
    const box = input.closest('.file-upload-box');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            if (file.type.startsWith('image/')) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="max-height:80px;max-width:80px;border-radius:8px;margin-top:5px;"><br><span class="file-name" style="font-size:0.82rem;font-weight:600;color:var(--text-dark);word-break:break-all;">' + file.name + '</span>';
            } else {
                preview.innerHTML = '<span style="color:var(--primary);font-weight:700;font-size:0.9rem;">📄 ' + file.name + '</span>';
            }
            box.classList.add('has-file');
        }

        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
        box.classList.remove('has-file');
    }
}

// ============================================================
// FORM SUBMIT VALIDATION
// Final check right before submit: makes sure every field
// marked "required" actually has a file attached.
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('documentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let allValid = true;
            const fileInputs = this.querySelectorAll('input[type="file"]');
            let errorMessages = [];

            fileInputs.forEach(function(input) {
                // Missing required file
                if (input.hasAttribute('required') && !input.files.length) {
                    const label = input.closest('.file-upload-box')?.querySelector('strong')?.textContent || 'document';
                    errorMessages.push('❌ ' + label + ' is required.');
                    allValid = false;
                    e.preventDefault();
                    return;
                }

                // Re-validate size/type in case something slipped through
                if (input.files.length > 0) {
                    const isValid = validateFileSize(input);
                    if (!isValid) {
                        allValid = false;
                        e.preventDefault();
                        return;
                    }
                }
            });

            if (!allValid && errorMessages.length > 0) {
                alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
                e.preventDefault();
            }
        });
    }
});

// ============================================================
// ALREADY UPLOADED FILES
// Shows every previously saved document inside its own card and
// drops the "required" attribute for it, so the candidate only
// has to upload what is still missing (or replace a file).
// ============================================================
const existingFiles = @json($existingFiles);

function showExistingFile(field, info) {
    const input = document.querySelector('input[type="file"][name="' + field + '"]');
    if (!input) return;

    const box = input.closest('.file-upload-box');
    const preview = document.getElementById(input.dataset.previewId);
    if (!box || !preview) return;

    let html = '';
    if (['png', 'jpg', 'jpeg'].includes(info.ext)) {
        html += '<a href="' + info.url + '" target="_blank" rel="noopener">' +
                '<img src="' + info.url + '" alt="Uploaded document"></a><br>';
    } else {
        html += '<a href="' + info.url + '" target="_blank" rel="noopener">📄 View uploaded file</a><br>';
    }
    html += '<span class="file-name" style="color:#27ae60;">✅ Uploaded — choose a new file to replace it</span>';

    preview.innerHTML = html;
    box.classList.add('has-file');
    input.removeAttribute('required');
    input.dataset.hasExisting = '1';
}

document.addEventListener('DOMContentLoaded', function () {
    Object.keys(existingFiles).forEach(function (field) {
        showExistingFile(field, existingFiles[field]);
    });
});
</script>
@endpush