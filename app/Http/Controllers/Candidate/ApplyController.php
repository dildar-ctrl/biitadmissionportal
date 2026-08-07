<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Candidate;
use App\Models\Program;
use App\Models\Application;

class ApplyController extends Controller
{
    public function index()
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

        // programs currently open for admission
        $programs = Program::where('status', 'open')->orderBy('name')->get();

        // this candidate's existing applications (id => Application) for quick lookup
        $myApplications = collect();
        if ($candidate) {
            $myApplications = Application::with('program')
                ->where('candidate_id', $candidate->id)
                ->get()
                ->keyBy('program_id');
        }

        return view('candidate.apply', compact('candidate', 'programs', 'myApplications'));
    }

    // Apply to a single program (one "Apply Now" button per program row)
    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:program,id',
        ]);

        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

        if (!$candidate) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile before applying.');
        }

        // if already applied to this program, just take them to their applications list
        $existing = Application::where('candidate_id', $candidate->id)
            ->where('program_id', $request->program_id)
            ->first();

        if ($existing) {
            return redirect()->route('apply.status');
        }

        $year = date('Y');
        $prefix = 'NUN' . $year;

        // find the highest existing sequence number for this year and increment it
        $lastApplication = Application::where('application_no', 'like', $prefix . '%')
            ->orderByDesc('application_no')
            ->first();

        $nextSequence = 1;
        if ($lastApplication) {
            $lastSequence = (int) substr($lastApplication->application_no, strlen($prefix));
            $nextSequence = $lastSequence + 1;
        }

        $applicationNo = $prefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

        Application::create([
            'candidate_id' => $candidate->id,
            'program_id' => $request->program_id,
            'application_no' => $applicationNo,
            'status' => 'pending', // admin will review and approve/reject later
            'applied_at' => now(),
        ]);

        return redirect()->route('apply.status')->with('success', 'Application submitted successfully!');
    }

    // "My Applications" list page — all applications the candidate has submitted
    public function myApplications()
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

        $myApplications = collect();
        if ($candidate) {
            $myApplications = Application::with('program', 'rollSlip')
                ->where('candidate_id', $candidate->id)
                ->orderByDesc('applied_at')
                ->get()
                ->map(function ($app) {
                    // flag whether a published result exists for this application,
                    // so the blade can show a "View Result" link
                    $app->has_published_result = \Illuminate\Support\Facades\DB::table('testattempt')
                        ->where('application_id', $app->id)
                        ->where('published', 1)
                        ->exists();
                    return $app;
                });
        }

        return view('candidate.application-status', compact('myApplications'));
    }

    // Printable application form (candidate details + education)
    public function download($applicationId)
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

        $application = Application::with('program', 'candidate.education')
            ->where('id', $applicationId)
            ->where('candidate_id', $candidate->id ?? 0)
            ->firstOrFail();

        return view('candidate.application-print', compact('application'));
    }
}