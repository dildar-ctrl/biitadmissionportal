<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Candidate;
use App\Models\DocumentType;
use App\Models\Document;
use App\Models\Application;
use App\Models\ApplicationChoice;
use App\Models\AdmissionSchedule;

class CandidateDashboardController extends Controller
{
    // Same session key ApplyController writes selected program IDs to.
    const PROGRAMS_SESSION_KEY = 'application.selected_programs';

    public function index()
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

        // ---- Submitted application (if final Submit has already happened) ----
        $currentSchedule = AdmissionSchedule::current();

$latestApplication = $candidate
    ? Application::with('choices.program')
        ->where('candidate_id', $candidate->id)
        ->whereHas('choices.program', function ($q) use ($currentSchedule) {
            $q->where('session_id', $currentSchedule?->sessionid);
        })
        ->latest()
        ->first()
    : null;

        $submittedChoices = $latestApplication ? $latestApplication->choices : collect();

        // ---- Step 1: Program Selection — either already submitted (DB) or
        // still sitting in session waiting for the rest of the flow ----
        $sessionProgramIds = session(self::PROGRAMS_SESSION_KEY, []);
        $programSelected = $submittedChoices->isNotEmpty() || !empty($sessionProgramIds);
        $programChoicesCount = $submittedChoices->isNotEmpty() ? $submittedChoices->count() : count($sessionProgramIds);

        $firstChoice = $submittedChoices->firstWhere('priority', 1);
        if ($firstChoice) {
            $selectedProgramName = $firstChoice->program->name ?? null;
        } elseif (!empty($sessionProgramIds)) {
            $firstProgram = \App\Models\Program::find($sessionProgramIds[0]);
            $selectedProgramName = $firstProgram->name ?? null;
        } else {
            $selectedProgramName = null;
        }

        // ---- Step 2: Profile — complete only when EVERY required field is filled ----
        $requiredProfileFields = [
            'fname', 'lname', 'cnic', 'dob', 'postal_address', 'permanent_address',
            'gender', 'mobile_no', 'religion', 'marital_status', 'nationality', 'medicalissue',
            'father_name', 'father_cnic', 'relationshipwithguardian', 'father_mobile_no',
            'emergencycontactname', 'emergencycontactrelationship', 'emergencycontact',
        ];

        $profileFilledCount = 0;
        if ($candidate) {
            foreach ($requiredProfileFields as $field) {
                if (!empty($candidate->{$field})) {
                    $profileFilledCount++;
                }
            }
        }
        $profileTotalFields = count($requiredProfileFields);
        $profileComplete = $candidate && $profileFilledCount === $profileTotalFields;

        // ---- Step 3: Documents — complete only when EVERY document type has been uploaded ----
        $documentsTotalTypes = DocumentType::count();
        $documentsUploadedCount = 0;
        $documentsComplete = false;
        if ($candidate) {
            $requiredDocTypeIds = DocumentType::pluck('id');
            $uploadedDocTypeIds = Document::where('candidate_id', $candidate->id)->pluck('documenttype_id')->unique();

            $documentsUploadedCount = $uploadedDocTypeIds->intersect($requiredDocTypeIds)->count();
            $documentsComplete = $requiredDocTypeIds->diff($uploadedDocTypeIds)->isEmpty() && $requiredDocTypeIds->isNotEmpty();
        }

        // ---- Final Submit — application_no only exists once the real Application row has been created ----
        $applicationSubmitted = $latestApplication !== null;
        $applicationNumber = $latestApplication->application_no ?? null;
        $applicationStatus = $latestApplication ? ucfirst($latestApplication->status) : 'Draft';

        // ---- Overall completion percentage across the four dashboard steps ----
        $stepsComplete = collect([$programSelected, $profileComplete, $documentsComplete, $applicationSubmitted])
            ->filter()->count();
        $completionPercent = (int) round(($stepsComplete / 4) * 100);

        // ---- Next step guidance, following the flow order:
        // Program -> Profile -> Documents -> Review -> Undertakings -> Submit ----
        if ($applicationSubmitted) {
            $nextStepText = 'Your application has been submitted. Track its status below.';
            $nextStepRoute = route('apply.status');
        } elseif (!$programSelected) {
            $nextStepText = 'Select up to 3 programs to begin your application.';
            $nextStepRoute = route('apply.index');
        } elseif (!$profileComplete) {
            $nextStepText = 'Complete your profile with personal and academic details.';
            $nextStepRoute = route('profile.edit');
        } elseif (!$documentsComplete) {
            $nextStepText = 'Upload your required documents.';
            $nextStepRoute = route('document.edit');
        } else {
            $nextStepText = 'Review your profile and accept the undertakings to submit.';
            $nextStepRoute = route('profile.review');
        }

        return view('dashboard', [
            'candidate' => $candidate,
            'applicationNumber' => $applicationNumber,
            'applicationStatus' => $applicationStatus,
            'completionPercent' => $completionPercent,
            'programSelected' => $programSelected,
            'selectedProgramName' => $selectedProgramName,
            'programChoicesCount' => $programChoicesCount,
            'profileComplete' => $profileComplete,
            'profileFilledCount' => $profileFilledCount,
            'profileTotalFields' => $profileTotalFields,
            'documentsComplete' => $documentsComplete,
            'documentsUploadedCount' => $documentsUploadedCount,
            'documentsTotalTypes' => $documentsTotalTypes,
            'applicationSubmitted' => $applicationSubmitted,
            'nextStepText' => $nextStepText,
            'nextStepRoute' => $nextStepRoute,
        ]);
    }
}
