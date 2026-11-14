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

        $allDocuments = Document::where('candidate_id', $candidate->id)->get();

        // Fixed, non-education documents (photo, CNIC, CNIC father/guardian) —
        // keyed by documenttype_id exactly as before.
        $documents = $allDocuments->whereNull('education_id')->keyBy('documenttype_id');

        // Per-education certificate/DMC status, keyed by education_id, so the
        // view can show "Already Uploaded" against the right degree block.
        // Uses the SAME fixed per-degree documenttype_id as store() below
        // (Matric -> 2/3, Intermediate -> 4/5) so the badge always lines up
        // with whatever id the file was actually saved under.
        $educationDocuments = [];
        foreach ($educations as $edu) {
            [$certTypeId, $dmcTypeId] = $this->resolveDocTypeIds($edu);

            $educationDocuments[$edu->id] = [
                'certificate' => $allDocuments->first(fn ($d) => $d->education_id == $edu->id && $d->documenttype_id == $certTypeId),
                'dmc' => $allDocuments->first(fn ($d) => $d->education_id == $edu->id && $d->documenttype_id == $dmcTypeId),
            ];
        }
        // Already uploaded files, keyed by form field name, so the view can show them in their cards
        $fieldByTypeId = [
            7 => 'photo',
            1 => 'cnicfront',
            8 => 'cnicback',
            9 => 'bform',
            6 => 'fathercnicfront',
            10 => 'fathercnicback',
        ];

        $existingFiles = [];
        foreach ($fieldByTypeId as $typeId => $field) {
            if (isset($documents[$typeId])) {
                $existingFiles[$field] = $this->existingFileInfo($documents[$typeId]);
            }
        }

        foreach ($educations as $edu) {
            foreach (['certificate', 'dmc'] as $kind) {
                $doc = $educationDocuments[$edu->id][$kind] ?? null;
                if ($doc) {
                    $existingFiles["{$kind}_{$edu->id}"] = $this->existingFileInfo($doc);
                }
            }
        }
        return view('candidate.document', [
            'candidate' => $candidate,
            'documents' => $documents,
            'educationDocuments' => $educationDocuments,
            'documentTypes' => $documentTypes,
            'educations' => $educations,
            'existingFiles' => $existingFiles,
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

        // The candidate either uploads CNIC (front + back) or, if they have
        // no CNIC, a single B-Form image.
        $noCnic = $request->boolean('nocnic');

        // -----------------------------------------------------------
        // Build validation rules.
        // Photo stays image-only, capped at 5MB.
        // Everything else can be PNG/JPG/JPEG/PDF — CNIC/B-Form capped at
        // 5MB, and certificates/DMCs capped at 10MB since scanned PDFs
        // tend to be larger than plain images.
        // A file is only required when nothing is stored yet for that
        // document, so the candidate never has to re-upload everything.
        // -----------------------------------------------------------
        $existingDocs = Document::where('candidate_id', $candidate->id)->get();
        $requiredIfMissing = function (int $typeId, $educationId = null) use ($existingDocs) {
            $exists = $existingDocs->contains(
                fn ($d) => $d->documenttype_id == $typeId && $d->education_id == $educationId
            );
            return $exists ? 'nullable' : 'required';
        };

        $fileRules = fn (int $typeId) => [$requiredIfMissing($typeId), 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120']; // 5MB

        $rules = [
            // Photo (documenttype_id = 7)
            'photo' => [
                $requiredIfMissing(7),
                'image',
                'mimes:png,jpg,jpeg',
                'max:5120', // 5MB
                'dimensions:min_width=150,min_height=150,ratio=1/1',
            ],

            // CNIC Father/Guardian Front (documenttype_id = 6)
            'fathercnicfront' => $fileRules(6),

            // CNIC Father/Guardian Back (documenttype_id = 10)
            'fathercnicback' => $fileRules(10),
        ];

        if ($noCnic) {
            // B-Form (documenttype_id = 9)
            $rules['bform'] = $fileRules(9);
        } else {
            // CNIC Front (documenttype_id = 1) and CNIC Back (documenttype_id = 8)
            $rules['cnicfront'] = $fileRules(1);
            $rules['cnicback'] = $fileRules(8);
        }

        $messages = [
            'photo.required' => 'Passport size photo is required.',
            'photo.image' => 'Photo must be a valid image file.',
            'photo.mimes' => 'Photo must be in PNG, JPG, or JPEG format.',
            'photo.max' => 'Photo size must not exceed 5MB.',
            'photo.dimensions' => 'Photo must be square (1:1 ratio) and at least 150x150 pixels.',

            'cnicfront.required' => 'CNIC front side is required.',
            'cnicfront.file' => 'CNIC front side must be a valid file.',
            'cnicfront.mimes' => 'CNIC front side must be in PNG, JPG, JPEG, or PDF format.',
            'cnicfront.max' => 'CNIC front side size must not exceed 5MB.',

            'cnicback.required' => 'CNIC back side is required.',
            'cnicback.file' => 'CNIC back side must be a valid file.',
            'cnicback.mimes' => 'CNIC back side must be in PNG, JPG, JPEG, or PDF format.',
            'cnicback.max' => 'CNIC back side size must not exceed 5MB.',

            'bform.required' => 'B-Form is required if you do not have a CNIC.',
            'bform.file' => 'B-Form must be a valid file.',
            'bform.mimes' => 'B-Form must be in PNG, JPG, JPEG, or PDF format.',
            'bform.max' => 'B-Form size must not exceed 5MB.',

            'fathercnicfront.required' => 'Father/Guardian CNIC front side is required.',
            'fathercnicfront.file' => 'Father/Guardian CNIC front side must be a valid file.',
            'fathercnicfront.mimes' => 'Father/Guardian CNIC front side must be in PNG, JPG, JPEG, or PDF format.',
            'fathercnicfront.max' => 'Father/Guardian CNIC front side size must not exceed 5MB.',

            'fathercnicback.required' => 'Father/Guardian CNIC back side is required.',
            'fathercnicback.file' => 'Father/Guardian CNIC back side must be a valid file.',
            'fathercnicback.mimes' => 'Father/Guardian CNIC back side must be in PNG, JPG, JPEG, or PDF format.',
            'fathercnicback.max' => 'Father/Guardian CNIC back side size must not exceed 5MB.',
        ];

        // ---------------------------------------------------------------
        // One Result (required unless already uploaded) + one optional DMC
        // upload per education entry the candidate has on file. Field names
        // are keyed by the education row's own id: certificate_{id} / dmc_{id}.
        // (Field name stays "certificate_" for backward compatibility —
        // only the label shown to the candidate was renamed to "Result"
        // in the view.)
        // ---------------------------------------------------------------
        foreach ($educations as $edu) {
            [$certTypeId] = $this->resolveDocTypeIds($edu);

            $rules["certificate_{$edu->id}"] = [$requiredIfMissing($certTypeId, $edu->id), 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'];
            $rules["dmc_{$edu->id}"] = ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'];

            $messages["certificate_{$edu->id}.required"] = $edu->degree . ' result is required.';
            $messages["certificate_{$edu->id}.file"] = $edu->degree . ' result must be a valid file.';
            $messages["certificate_{$edu->id}.mimes"] = $edu->degree . ' result must be in PNG, JPG, JPEG, or PDF format.';
            $messages["certificate_{$edu->id}.max"] = $edu->degree . ' result size must not exceed 10MB.';

            $messages["dmc_{$edu->id}.required"] = $edu->degree . ' DMC is required.';
            $messages["dmc_{$edu->id}.file"] = $edu->degree . ' DMC must be a valid file.';
            $messages["dmc_{$edu->id}.mimes"] = $edu->degree . ' DMC must be in PNG, JPG, JPEG, or PDF format.';
            $messages["dmc_{$edu->id}.max"] = $edu->degree . ' DMC size must not exceed 10MB.';
        }

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
            // If the candidate switched between CNIC and B-Form, remove the
            // documents of the mode that is no longer used.
            $this->deleteDocuments($candidate->id, $noCnic ? [1, 8] : [9]);

            // Mapping: field_name => documenttype_id (fixed, non-education documents)
            $documentMap = [
                'photo' => 7,              // Passport Size Photo
                'fathercnicfront' => 6,    // CNIC Father/Guardian Front
                'fathercnicback' => 10,    // CNIC Father/Guardian Back
            ];

            if ($noCnic) {
                $documentMap['bform'] = 9;      // B-Form
            } else {
                $documentMap['cnicfront'] = 1;  // CNIC Front
                $documentMap['cnicback'] = 8;   // CNIC Back
            }

            // Only files uploaded in this request replace the stored ones;
            // everything else stays untouched.
            foreach ($documentMap as $field => $docTypeId) {
                if ($request->hasFile($field)) {
                    $this->replaceDocument($candidate->id, $docTypeId, $request->file($field));
                }
            }

            // ---------------------------------------------------------------
            // Save one Result + one DMC per education entry, using a FIXED
            // documenttype_id per degree (Matric -> 2/3, Intermediate ->
            // 4/5).
            // ---------------------------------------------------------------
            foreach ($educations as $edu) {
                [$certTypeId, $dmcTypeId] = $this->resolveDocTypeIds($edu);

                if ($request->hasFile("certificate_{$edu->id}")) {
                    $this->replaceDocument($candidate->id, $certTypeId, $request->file("certificate_{$edu->id}"), $edu->id);
                }
                if ($request->hasFile("dmc_{$edu->id}")) {
                    $this->replaceDocument($candidate->id, $dmcTypeId, $request->file("dmc_{$edu->id}"), $edu->id);
                }
            }

            return redirect()->route('profile.review')->with('success', 'Documents uploaded successfully! Please review your application.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload documents: ' . $e->getMessage());
        }
    }

    /**
     * Returns [certificateTypeId, dmcTypeId] for a given education row.
     * Matric -> [2, 3] (Matric Character Certificate, Matric Result)
     * Intermediate -> [4, 5] (Intermediate Character Certificate, Intermediate Result)
     *
     * No generic "Certificate"(9)/"DMC"(10) fallback is used anymore —
     * only these two fixed, dedicated documenttype ids are ever written
     * to the `documents` table for education documents.
     */
    private function resolveDocTypeIds(Education $edu): array
    {
        $degree = strtolower($edu->degree ?? '');

        if (str_contains($degree, 'matric') || str_contains($degree, 'o level')) {
            return [2, 3];
        }

        // Everything else (Intermediate, FSc, A Level, HSSC, and any
        // additional degree added via "Add More") maps to the
        // Intermediate documenttype ids.
        return [4, 5];
    }
    /**
     * Builds the URL and extension the view needs to display a stored file.
     */
    private function existingFileInfo(Document $doc): array
    {
        return [
            'url' => route('document.file', $doc->id),
            'ext' => strtolower(pathinfo($doc->document_path, PATHINFO_EXTENSION)),
        ];
    }

    /**
     * Deletes every stored document (file and row) of the given types.
     */
    private function deleteDocuments(int $candidateId, array $typeIds): void
    {
        $docs = Document::where('candidate_id', $candidateId)
            ->whereIn('documenttype_id', $typeIds)
            ->get();

        foreach ($docs as $doc) {
            Storage::disk('public')->delete($doc->document_path);
            $doc->delete();
        }
    }

    /**
     * Removes the previously stored file of this document (if any) and saves the new one.
     */
    private function replaceDocument($candidateId, $documentTypeId, $file, $educationId = null)
    {
        $oldDocs = Document::where('candidate_id', $candidateId)
            ->where('documenttype_id', $documentTypeId)
            ->when(
                $educationId === null,
                fn ($q) => $q->whereNull('education_id'),
                fn ($q) => $q->where('education_id', $educationId)
            )
            ->get();

        foreach ($oldDocs as $old) {
            Storage::disk('public')->delete($old->document_path);
            $old->delete();
        }

        return $this->saveDocument($candidateId, $documentTypeId, $file, $educationId);
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