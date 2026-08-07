<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Candidate;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Education;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function edit()
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

        if (!$candidate) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile first.');
        }

        $educations = Education::where('candidate_id', $candidate->id)->orderBy('id')->get();

        if ($educations->count() === 0) {
            return redirect()->route('profile.edit')->with('error', 'Please add your education qualifications first.');
        }

        $documentTypes = DocumentType::orderBy('type')->get();

        // 'Certificate' and 'DMC' are generic, reusable document types — every
        // education entry (Matric, Intermediate, Bachelor's, Master's, or any
        // degree added later via "Add More") gets its own Certificate/DMC pair,
        // told apart by the new documents.education_id column rather than by
        // a dedicated documenttype row per degree.
        $certTypeId = DocumentType::firstOrCreate(['type' => 'Certificate'])->id;
        $dmcTypeId = DocumentType::firstOrCreate(['type' => 'DMC'])->id;

        $allDocuments = Document::where('candidate_id', $candidate->id)->get();

        // Fixed, non-education documents (photo, CNIC, domicile, etc.) — keyed
        // by documenttype_id exactly as before.
        $documents = $allDocuments->whereNull('education_id')->keyBy('documenttype_id');

        // Per-education certificate/DMC status, keyed by education_id, so the
        // view can show "Already Uploaded" against the right degree block.
        $educationDocuments = [];
        foreach ($educations as $edu) {
            $educationDocuments[$edu->id] = [
                'certificate' => $allDocuments->first(fn ($d) => $d->education_id == $edu->id && $d->documenttype_id == $certTypeId),
                'dmc' => $allDocuments->first(fn ($d) => $d->education_id == $edu->id && $d->documenttype_id == $dmcTypeId),
            ];
        }

        return view('candidate.document', [
            'candidate' => $candidate,
            'documents' => $documents,
            'educationDocuments' => $educationDocuments,
            'documentTypes' => $documentTypes,
            'educations' => $educations,
        ]);
    }

    public function store(Request $request)
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

        if (!$candidate) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile first.');
        }

        $educations = Education::where('candidate_id', $candidate->id)->orderBy('id')->get();

        // -----------------------------------------------------------
        // Build validation rules.
        // Photo stays image-only, capped at 5MB.
        // Everything else can be PNG/JPG/JPEG/PDF — CNIC capped at 5MB,
        // and certificates/DMCs/additional docs capped at 10MB since
        // scanned PDFs tend to be larger than plain images.
        // -----------------------------------------------------------
        $rules = [
            // Photo (documenttype_id = 2)
            'photo' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg',
                'max:5120', // 5MB
                'dimensions:min_width=150,min_height=150,ratio=1/1',
            ],

            // CNIC (documenttype_id = 1)
            'cnic' => [
                'required',
                'file',
                'mimes:png,jpg,jpeg,pdf',
                'max:5120', // 5MB
            ],
        ];

        $messages = [
            'photo.required' => 'Passport size photo is required.',
            'photo.image' => 'Photo must be a valid image file.',
            'photo.mimes' => 'Photo must be in PNG, JPG, or JPEG format.',
            'photo.max' => 'Photo size must not exceed 5MB.',
            'photo.dimensions' => 'Photo must be square (1:1 ratio) and at least 150x150 pixels.',

            'cnic.required' => 'CNIC / B-Form is required.',
            'cnic.file' => 'CNIC / B-Form must be a valid file.',
            'cnic.mimes' => 'CNIC / B-Form must be in PNG, JPG, JPEG, or PDF format.',
            'cnic.max' => 'CNIC / B-Form size must not exceed 5MB.',

            'domicile.mimes' => 'Domicile Certificate must be in PNG, JPG, JPEG, or PDF format.',
            'domicile.max' => 'Domicile Certificate size must not exceed 10MB.',

            'character.mimes' => 'Character Certificate must be in PNG, JPG, JPEG, or PDF format.',
            'character.max' => 'Character Certificate size must not exceed 10MB.',

            'migration.mimes' => 'Migration Certificate must be in PNG, JPG, JPEG, or PDF format.',
            'migration.max' => 'Migration Certificate size must not exceed 10MB.',

            'equivalence.mimes' => 'Equivalence Certificate must be in PNG, JPG, JPEG, or PDF format.',
            'equivalence.max' => 'Equivalence Certificate size must not exceed 10MB.',

            'disability.mimes' => 'Disability Certificate must be in PNG, JPG, JPEG, or PDF format.',
            'disability.max' => 'Disability Certificate size must not exceed 10MB.',
        ];

        // ---------------------------------------------------------------
        // One Certificate (required) + DMC (optional) upload per education
        // entry the candidate has on file — however many they added via
        // "Add More" on the Profile page. Field names are keyed by the
        // education row's own id: certificate_{id} / dmc_{id}.
        // ---------------------------------------------------------------
        foreach ($educations as $edu) {
            $rules["certificate_{$edu->id}"] = ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'];
            $rules["dmc_{$edu->id}"] = ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'];

            $messages["certificate_{$edu->id}.required"] = $edu->degree . ' certificate is required.';
            $messages["certificate_{$edu->id}.file"] = $edu->degree . ' certificate must be a valid file.';
            $messages["certificate_{$edu->id}.mimes"] = $edu->degree . ' certificate must be in PNG, JPG, JPEG, or PDF format.';
            $messages["certificate_{$edu->id}.max"] = $edu->degree . ' certificate size must not exceed 10MB.';

            $messages["dmc_{$edu->id}.file"] = $edu->degree . ' DMC must be a valid file.';
            $messages["dmc_{$edu->id}.mimes"] = $edu->degree . ' DMC must be in PNG, JPG, JPEG, or PDF format.';
            $messages["dmc_{$edu->id}.max"] = $edu->degree . ' DMC size must not exceed 10MB.';
        }

        // Additional documents (optional)
        $rules['domicile'] = ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'];
        $rules['character'] = ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'];
        $rules['migration'] = ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'];
        $rules['equivalence'] = ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'];
        $rules['disability'] = ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'];

        $validator = Validator::make($request->all(), $rules, $messages);

        // -----------------------------------------------------------
        // Extra security validation — catches files whose extension was
        // renamed to look safe (e.g. malware.php renamed to photo.jpg)
        // by inspecting the ACTUAL file content, not just its name.
        // -----------------------------------------------------------
        $validator->after(function ($validator) use ($request) {
            foreach ($request->allFiles() as $key => $file) {
                if (is_array($file)) {
                    foreach ($file as $subFile) {
                        $this->validateFileSecurity($subFile, $key, $validator);
                    }
                } else {
                    $this->validateFileSecurity($file, $key, $validator);
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Save all documents
        try {
            // Delete old documents
            $oldPaths = Document::where('candidate_id', $candidate->id)->pluck('document_path');
            foreach ($oldPaths as $oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            Document::where('candidate_id', $candidate->id)->delete();

            // Mapping: field_name => documenttype_id (fixed, non-education documents)
            $documentMap = [
                'photo' => 2,
                'cnic' => 1,
                'domicile' => 7,
                'character' => 8,
                'migration' => 9,
                'equivalence' => 10,
                'disability' => 11,
            ];

            foreach ($documentMap as $field => $docTypeId) {
                if ($request->hasFile($field)) {
                    $this->saveDocument($candidate->id, $docTypeId, $request->file($field));
                }
            }

            // Save one Certificate + optional DMC per education entry
            $certTypeId = DocumentType::firstOrCreate(['type' => 'Certificate'])->id;
            $dmcTypeId = DocumentType::firstOrCreate(['type' => 'DMC'])->id;

            foreach ($educations as $edu) {
                if ($request->hasFile("certificate_{$edu->id}")) {
                    $this->saveDocument($candidate->id, $certTypeId, $request->file("certificate_{$edu->id}"), $edu->id);
                }
                if ($request->hasFile("dmc_{$edu->id}")) {
                    $this->saveDocument($candidate->id, $dmcTypeId, $request->file("dmc_{$edu->id}"), $edu->id);
                }
            }

            return redirect()->route('profile.view')->with('success', 'Documents uploaded successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload documents: ' . $e->getMessage());
        }
    }

    private function saveDocument($candidateId, $documentTypeId, $file, $educationId = null)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = "candidate_documents/" . $filename;

        Storage::disk('public')->put($path, file_get_contents($file));

        return Document::create([
            'candidate_id' => $candidateId,
            'education_id' => $educationId,
            'documenttype_id' => $documentTypeId,
            'document_path' => $path,
            'uploaded_at' => now(),
        ]);
    }

    /**
     * Inspects the actual content of an uploaded file to make sure it
     * really is what its extension claims to be. A renamed .php or .js
     * file will fail these checks even if its extension says .jpg/.pdf.
     */
    private function validateFileSecurity($file, string $fieldName, $validator)
    {
        $allowedMimes = [
            'image/png',
            'image/jpeg', // covers both .jpg and .jpeg
            'application/pdf',
        ];

        $allowedExtensions = ['png', 'jpg', 'jpeg', 'pdf'];
        $dangerousExtensions = ['exe', 'php', 'php3', 'php4', 'php5', 'phtml', 'js', 'sh', 'bat', 'cmd', 'com', 'scr', 'msi', 'html', 'htm', 'svg'];

        // 1) Check the extension the file claims to have
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, $dangerousExtensions)) {
            $validator->errors()->add($fieldName, 'Executable or script files are not allowed for this upload.');
            return;
        }

        if (!in_array($extension, $allowedExtensions)) {
            $validator->errors()->add($fieldName, 'Only PNG, JPG, JPEG, and PDF files are allowed.');
            return;
        }

        // 2) Check the REAL mime type from the file's content (magic bytes),
        // not just what the browser/extension claims — this is what catches
        // a renamed .php file pretending to be a .jpg
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMimeType = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        if (!in_array($realMimeType, $allowedMimes)) {
            $validator->errors()->add($fieldName, 'This file does not appear to be a genuine image or PDF. Its content does not match its file extension.');
            return;
        }

        // 3) Verify the file signature (magic bytes) matches the extension
        // — an extra layer on top of finfo for image/PDF files specifically
        $handle = fopen($file->getRealPath(), 'rb');
        $headerBytes = fread($handle, 8);
        fclose($handle);

        $isValidSignature = false;
        if ($extension === 'png' && str_starts_with($headerBytes, "\x89PNG")) {
            $isValidSignature = true;
        } elseif (in_array($extension, ['jpg', 'jpeg']) && str_starts_with($headerBytes, "\xFF\xD8\xFF")) {
            $isValidSignature = true;
        } elseif ($extension === 'pdf' && str_starts_with($headerBytes, '%PDF')) {
            $isValidSignature = true;
        }

        if (!$isValidSignature) {
            $validator->errors()->add($fieldName, 'This file appears to be corrupted or is not a genuine ' . strtoupper($extension) . ' file.');
            return;
        }

        // 4) Scan the file content for embedded PHP code (a common trick
        // is to hide a PHP script inside an otherwise valid-looking image).
        // NOTE: this used to scan the ENTIRE file's raw bytes. For a real
        // photo/PDF (several MB of compressed binary data), the odds of a
        // short text pattern like "<?=" turning up purely by chance are
        // high enough that genuine uploads were being rejected. Malicious
        // payloads are virtually always prepended or appended to the file
        // (the classic "image polyglot" trick), so scanning just the first
        // and last 16KB catches the same attacks with far fewer false
        // positives on legitimate files.
        $scanChunkSize = 16384; // 16KB
        $fileSize = filesize($file->getRealPath());

        $handle = fopen($file->getRealPath(), 'rb');
        $startChunk = fread($handle, min($scanChunkSize, $fileSize));
        $endChunk = '';
        if ($fileSize > $scanChunkSize) {
            fseek($handle, -min($scanChunkSize, $fileSize), SEEK_END);
            $endChunk = fread($handle, $scanChunkSize);
        }
        fclose($handle);

        $contentToScan = $startChunk . $endChunk;

        if (preg_match('/<\?php|<\?=|<script\s+language\s*=\s*["\']?php/i', $contentToScan)) {
            $validator->errors()->add($fieldName, 'This file contains disallowed script code and cannot be uploaded.');
            return;
        }

        // 5) For image files specifically, also check for embedded
        // JavaScript/HTML that could be used for stored XSS if the file
        // is ever displayed directly in a browser
        if (in_array($realMimeType, ['image/png', 'image/jpeg'])) {
            if (preg_match('/<script|javascript:|onerror\s*=|onload\s*=|onclick\s*=|<iframe|<embed|<object/i', $contentToScan)) {
                $validator->errors()->add($fieldName, 'This file contains disallowed embedded code and cannot be uploaded.');
                return;
            }
        }
    }

    public function showFile(Document $document)
    {
        // This route is used from two different places:
        //  1) The candidate's own "View Profile" page — auth:web guard.
        //  2) The admin "Applications" review modal — auth:admin guard.
        if (Auth::guard('admin')->check()) {
            // Admin is allowed to view any candidate's document.
        } else {
            $candidateLoginId = Auth::guard('web')->id();
            $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

            abort_if(!$candidate || $document->candidate_id !== $candidate->id, 403);
        }

        abort_unless(Storage::disk('public')->exists($document->document_path), 404);

        return Storage::disk('public')->response($document->document_path);
    }
}