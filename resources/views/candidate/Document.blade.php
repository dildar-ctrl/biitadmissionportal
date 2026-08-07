@extends('admin.layout')

@section('title', 'Documents')

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

{{-- Flash message after a successful upload --}}
@if(session('success'))
<div class="alert-success" style="padding:15px;border-radius:8px;margin-bottom:20px;">
    {{ session('success') }}
</div>
@endif

{{-- Flash message for general (non-validation) errors, e.g. missing profile/education --}}
@if(session('error'))
<div class="alert-danger" style="padding:15px;border-radius:8px;margin-bottom:20px;">
    {{ session('error') }}
</div>
@endif

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

                @if(isset($documents[2]))
                    <span class="document-status uploaded">✅ Already Uploaded</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ===================== CNIC / B-Form ===================== --}}
    <div class="form-section">
        <h6>🪪 CNIC / B-Form <span class="required-star">*</span></h6>
        <div class="row">
            <div class="col-md-6">
                <div class="file-upload-box {{ $errors->has('cnic') ? 'has-error' : '' }}" id="cnic-box">
                    <div class="upload-icon">🪪</div>
                    <p><strong>Upload CNIC / B-Form</strong></p>
                    <p style="font-size:0.8rem;color:var(--text-muted);">
                        PNG, JPG, JPEG, PDF • Max 5MB
                    </p>
                    <input type="file" name="cnic" accept=".png,.jpg,.jpeg,.pdf" 
                           data-max-size="5242880" 
                           data-preview-id="cnic-preview"
                           onchange="validateFileSize(this)" required>
                    <div id="cnic-preview" class="file-preview"></div>
                </div>

                @error('cnic')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror

                @if(isset($documents[1]))
                    <span class="document-status uploaded">✅ Already Uploaded</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ===================== Education Documents ===================== --}}
    {{-- One Certificate (required) + DMC (optional) block per education
         entry the candidate has on file — Matric, Intermediate, and any
         extra degree added via "Add More" on the Profile page. No more
         hardcoded Matric/Intermediate-only detection: every row in
         $educations gets its own upload block automatically. --}}
    <div class="form-section">
        <h6>🎓 Education Documents <span class="required-star">*</span></h6>
        <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:15px;">
            Please upload a certificate (required) and DMC (optional) for every degree listed in your profile.
            <br>Allowed: PNG, JPG, JPEG, PDF • Max 10MB each
        </p>

        @foreach($educations as $edu)
        <div style="border-left:3px solid var(--primary);padding-left:15px;margin-bottom:20px;">
            <h6 style="color:var(--primary);font-weight:700;margin-bottom:15px;">📄 {{ $edu->degree }} Documents</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="file-upload-box {{ $errors->has('certificate_'.$edu->id) ? 'has-error' : '' }}" id="certificate-{{ $edu->id }}-box">
                        <div class="upload-icon">📄</div>
                        <p><strong>{{ $edu->degree }} Certificate <span class="required-star">*</span></strong></p>
                        <p style="font-size:0.8rem;color:var(--text-muted);">
                            PNG, JPG, JPEG, PDF • Max 10MB
                        </p>
                        <input type="file" name="certificate_{{ $edu->id }}" accept=".png,.jpg,.jpeg,.pdf"
                               data-max-size="10485760"
                               data-preview-id="certificate-{{ $edu->id }}-preview"
                               onchange="validateFileSize(this)" required>
                        <div id="certificate-{{ $edu->id }}-preview" class="file-preview"></div>
                    </div>

                    @error('certificate_'.$edu->id)
                        <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror

                    @if(!empty($educationDocuments[$edu->id]['certificate']))
                        <span class="document-status uploaded">✅ Already Uploaded</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="file-upload-box {{ $errors->has('dmc_'.$edu->id) ? 'has-error' : '' }}" id="dmc-{{ $edu->id }}-box">
                        <div class="upload-icon">📄</div>
                        <p><strong>{{ $edu->degree }} DMC <span style="font-weight:400;font-size:0.7rem;color:var(--text-muted);">(Optional)</span></strong></p>
                        <p style="font-size:0.8rem;color:var(--text-muted);">
                            PNG, JPG, JPEG, PDF • Max 10MB
                        </p>
                        <input type="file" name="dmc_{{ $edu->id }}" accept=".png,.jpg,.jpeg,.pdf"
                               data-max-size="10485760"
                               data-preview-id="dmc-{{ $edu->id }}-preview"
                               onchange="validateFileSize(this)">
                        <div id="dmc-{{ $edu->id }}-preview" class="file-preview"></div>
                    </div>

                    @error('dmc_'.$edu->id)
                        <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                    @enderror

                    @if(!empty($educationDocuments[$edu->id]['dmc']))
                        <span class="document-status uploaded">✅ Already Uploaded</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ===================== Additional Documents (all optional) ===================== --}}
    <div class="form-section">
        <h6>📎 Additional Documents <span style="font-weight:400;font-size:0.8rem;color:var(--text-muted);">(Optional)</span></h6>
        <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:15px;">
            Upload additional documents if applicable.
            <br>Allowed: PNG, JPG, JPEG, PDF • Max 10MB each
        </p>
        
        <div class="row">
            <div class="col-md-4">
                <div class="file-upload-box {{ $errors->has('domicile') ? 'has-error' : '' }}" id="domicile-box">
                    <div class="upload-icon">🏠</div>
                    <p><strong>Domicile Certificate</strong></p>
                    <input type="file" name="domicile" accept=".png,.jpg,.jpeg,.pdf" 
                           data-max-size="10485760" 
                           data-preview-id="domicile-preview"
                           onchange="validateFileSize(this)">
                    <div id="domicile-preview" class="file-preview"></div>
                </div>

                @error('domicile')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror

                @if(isset($documents[7]))
                    <span class="document-status uploaded">✅ Already Uploaded</span>
                @endif
            </div>
            <div class="col-md-4">
                <div class="file-upload-box {{ $errors->has('character') ? 'has-error' : '' }}" id="character-box">
                    <div class="upload-icon">⭐</div>
                    <p><strong>Character Certificate</strong></p>
                    <input type="file" name="character" accept=".png,.jpg,.jpeg,.pdf" 
                           data-max-size="10485760" 
                           data-preview-id="character-preview"
                           onchange="validateFileSize(this)">
                    <div id="character-preview" class="file-preview"></div>
                </div>

                @error('character')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror

                @if(isset($documents[8]))
                    <span class="document-status uploaded">✅ Already Uploaded</span>
                @endif
            </div>
            <div class="col-md-4">
                <div class="file-upload-box {{ $errors->has('migration') ? 'has-error' : '' }}" id="migration-box">
                    <div class="upload-icon">🔄</div>
                    <p><strong>Migration Certificate</strong></p>
                    <input type="file" name="migration" accept=".png,.jpg,.jpeg,.pdf" 
                           data-max-size="10485760" 
                           data-preview-id="migration-preview"
                           onchange="validateFileSize(this)">
                    <div id="migration-preview" class="file-preview"></div>
                </div>

                @error('migration')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror

                @if(isset($documents[9]))
                    <span class="document-status uploaded">✅ Already Uploaded</span>
                @endif
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="file-upload-box {{ $errors->has('equivalence') ? 'has-error' : '' }}" id="equivalence-box">
                    <div class="upload-icon">📊</div>
                    <p><strong>Equivalence Certificate</strong></p>
                    <p style="font-size:0.7rem;color:var(--text-muted);">For O/A Levels only</p>
                    <input type="file" name="equivalence" accept=".png,.jpg,.jpeg,.pdf" 
                           data-max-size="10485760" 
                           data-preview-id="equivalence-preview"
                           onchange="validateFileSize(this)">
                    <div id="equivalence-preview" class="file-preview"></div>
                </div>

                @error('equivalence')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror

                @if(isset($documents[10]))
                    <span class="document-status uploaded">✅ Already Uploaded</span>
                @endif
            </div>
            <div class="col-md-4">
                <div class="file-upload-box {{ $errors->has('disability') ? 'has-error' : '' }}" id="disability-box">
                    <div class="upload-icon">♿</div>
                    <p><strong>Disability Certificate</strong></p>
                    <p style="font-size:0.7rem;color:var(--text-muted);">If applicable</p>
                    <input type="file" name="disability" accept=".png,.jpg,.jpeg,.pdf" 
                           data-max-size="10485760" 
                           data-preview-id="disability-preview"
                           onchange="validateFileSize(this)">
                    <div id="disability-preview" class="file-preview"></div>
                </div>

                @error('disability')
                    <span class="field-error"><i class="fas fa-triangle-exclamation me-1"></i>{{ $message }}</span>
                @enderror

                @if(isset($documents[11]))
                    <span class="document-status uploaded">✅ Already Uploaded</span>
                @endif
            </div>
        </div>
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
</script>
@endpush