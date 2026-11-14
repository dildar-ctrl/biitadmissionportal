<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\ApplicationChoice;
use App\Models\CandidateLogin;
use App\Models\Candidate;
use App\Models\Education;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminApplicationController extends Controller
{
    /**
     * List all applications
     */
    public function index(Request $request)
{
    $status = $request->query('status');
    $search = $request->query('search');
    $profile = $request->query('profile'); // 'complete' / 'incomplete' / null

    $sessionId = $request->query('session_id');

$applications = Application::with([
        'candidate.education',
        'candidate.documents.documentType',
        'choices.program',
    ])
    ->when($status, function ($query) use ($status) {
        $query->where('status', $status);
    })
    ->when($sessionId, function ($query) use ($sessionId) {
        $query->whereHas('choices.program', function ($q) use ($sessionId) {
            $q->where('session_id', $sessionId);
        });
    })
    ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('application_no', 'like', "%{$search}%")
                ->orWhereHas('candidate', function ($cq) use ($search) {
                    $cq->where('fname', 'like', "%{$search}%")
                        ->orWhere('lname', 'like', "%{$search}%");
                });
        });
    })
    ->orderByDesc('applied_at')
    ->get();

    if ($profile === 'complete') {
        $applications = $applications->filter(fn($app) => $app->candidate && $app->candidate->isProfileComplete());
    } elseif ($profile === 'incomplete') {
        $applications = $applications->filter(fn($app) => !$app->candidate || !$app->candidate->isProfileComplete());
    }

    $page = (int) $request->query('page', 1);
    $perPage = 20;
    $applications = new \Illuminate\Pagination\LengthAwarePaginator(
        $applications->forPage($page, $perPage)->values(),
        $applications->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    $programs = \App\Models\Program::where('status', 'open')->orderBy('name')->get();

    return view('admin.applications', compact('applications', 'status', 'profile', 'programs','sessionId'));
}
/**
 * Offline / Call Entry — admin manually registers a candidate + application
 * for someone who called in instead of applying online.
 * Reuses the same signup pattern (AuthController@signup) and the same
 * application_no generation logic (UndertakingsController@submitApplication).
 */
public function storeOffline(Request $request)
{
    $request->validate([
        'fname' => 'required|string|max:100',
        'lname' => 'required|string|max:100',
        'cnic' => 'required|string|max:20|unique:candidatelogin,cnic',
        'mobile_no' => 'required|string|max:20',
        'email' => 'required|email|max:150|unique:candidatelogin,email',
        'program_ids' => 'required|array|min:1|max:3',
        'program_ids.*' => 'required|distinct|exists:program,id',
        'intermediate_percentage' => 'required|numeric|min:0|max:100',
    ], [
        'cnic.unique' => 'A candidate with this CNIC already has an account.',
        'email.unique' => 'A candidate with this email already has an account.',
        'program_ids.max' => 'A candidate can select a maximum of 3 programs.',
    ]);

    $tempPassword = Str::random(8);

    $application = DB::transaction(function () use ($request, $tempPassword) {

        // 1. Create login account (same as normal signup, just admin-entered)
        $candidateLogin = CandidateLogin::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'cnic' => $request->cnic,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
        ]);

        // 2. Create candidate profile
        $candidate = Candidate::create([
            'candidate_login_id' => $candidateLogin->id,
            'fname' => $request->fname,
            'lname' => $request->lname,
            'cnic' => $request->cnic,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
        ]);

        // 3. Minimal Intermediate education record (percentage only, as call entry doesn't collect full detail)
        Education::create([
    'candidate_id' => $candidate->id,
    'degree' => 'Intermediate',
    'boardname' => 'N/A (Offline Entry)',
    'majorsubjects' => 'N/A',
    'date_of_passing' => now(),
    'obtained_marks' => $request->intermediate_percentage,
    'total_marks' => 100,
    'percentage' => $request->intermediate_percentage,
]);

        // 4. application_no — identical logic to UndertakingsController@submitApplication
        $year = date('Y');
        $prefix = 'BIIT' . $year;

               $lastApplication = Application::where('application_no', 'REGEXP', '^' . $prefix . '[0-9]{4}$')
            ->orderByRaw('CAST(SUBSTRING(application_no, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->lockForUpdate()
            ->first();

        $nextSequence = 1;
        if ($lastApplication) {
            $nextSequence = ((int) substr($lastApplication->application_no, strlen($prefix))) + 1;
        }

        $applicationNo = $prefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

        $application = Application::create([
            'candidate_id' => $candidate->id,
            'application_no' => $applicationNo,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        // 5. Program choices
        foreach (array_values($request->program_ids) as $index => $programId) {
            ApplicationChoice::create([
                'application_id' => $application->id,
                'program_id' => $programId,
                'priority' => $index + 1,
                'admin_status' => 'pending',
            ]);
        }

        return $application;
    });

    return redirect()->back()->with('success',
        'Offline application created — Application No: ' . $application->application_no .
        ' | Temporary Password: ' . $tempPassword .
        ' — please share these with the candidate over the phone.'
    );
}
    /**
     * View single application with all program choices
     */
    public function show(Application $application)
    {
        $application->load('candidate.education', 'candidate.documents', 'choices.program');

        return view('admin.application-detail', compact('application'));
    }

    /**
     * Admin reviews a specific program choice (approve/reject) - used for
     * one-off overrides outside the main approve flow, if ever needed.
     */
    public function reviewChoice(Request $request, ApplicationChoice $choice)
    {
        $request->validate([
            'admin_status' => 'required|in:pending,approved,rejected',
        ]);

        $choice->update(['admin_status' => $request->admin_status]);

        return redirect()->back()->with('success',
            'Program "' . $choice->program->name . '" has been ' . $request->admin_status . '.'
        );
    }

    /**
     * Approve/Reject multiple choices at once (optional)
     */
    public function bulkReview(Request $request, Application $application)
    {
        $request->validate([
            'choices' => 'required|array',
            'choices.*.id' => 'required|exists:applicationchoices,id',
            'choices.*.admin_status' => 'required|in:approved,rejected',
        ]);

        foreach ($request->choices as $choiceData) {
            ApplicationChoice::where('id', $choiceData['id'])
                ->where('application_id', $application->id)
                ->update(['admin_status' => $choiceData['admin_status']]);
        }

        return redirect()->back()->with('success', 'Choices updated successfully.');
    }

    /**
     * Admin approves the application FOR ONE SPECIFIC PROGRAM CHOICE.
     * That chosen choice -> admin_status = approved.
     * Every other choice on the same application -> admin_status = rejected (automatic).
     * The overall application -> status = approved.
     */
    public function approve(Request $request, Application $application)
{
    $request->validate([
        'choice_ids' => 'required|array|min:1',
        'choice_ids.*' => 'exists:applicationchoices,id',
    ]);

    // Approve the selected choices
    ApplicationChoice::where('application_id', $application->id)
        ->whereIn('id', $request->choice_ids)
        ->update(['admin_status' => 'approved']);

    // Auto-reject every other choice on this application
    ApplicationChoice::where('application_id', $application->id)
        ->whereNotIn('id', $request->choice_ids)
        ->update(['admin_status' => 'rejected']);

    $application->update(['status' => 'approved']);

    return redirect()->back()->with('success',
        'Application ' . $application->application_no . ' approved for ' . count($request->choice_ids) . ' program(s). Remaining choices were automatically rejected.'
    );
}

    /**
     * Admin rejects the WHOLE application - every program choice on it
     * is rejected too, so choice-level status always stays consistent
     * with the overall application status.
     */
    public function reject(Application $application)
    {
        $application->update(['status' => 'rejected']);
        $application->choices()->update(['admin_status' => 'rejected']);

        return redirect()->back()->with('success',
            'Application ' . $application->application_no . ' marked as rejected.'
        );
    }
    /**
 * Print the application form + all uploaded documents.
 * Only available after the application has been approved or rejected.
 */
public function print(Application $application)
{
    if (!in_array($application->status, ['approved', 'rejected'])) {
        return redirect()->back()->with('error', 'Print is available only after the application is approved or rejected.');
    }

    $application->load(['candidate.education', 'candidate.documents.documentType', 'choices.program']);

    $candidate = $application->candidate;
    $includeDocuments = true;

    return view('candidate.application-print', compact('application', 'candidate', 'includeDocuments'));
}

    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = [
            'total_applications' => Application::count(),
            'pending_applications' => Application::where('status', 'pending')->count(),
            'approved_applications' => Application::where('status', 'approved')->count(),
            'rejected_applications' => Application::where('status', 'rejected')->count(),
            'total_choices' => ApplicationChoice::count(),
            'approved_choices' => ApplicationChoice::where('admin_status', 'approved')->count(),
            'rejected_choices' => ApplicationChoice::where('admin_status', 'rejected')->count(),
            'pending_choices' => ApplicationChoice::where('admin_status', 'pending')->count(),
        ];

        return view('admin.statistics', compact('stats'));
    }
}