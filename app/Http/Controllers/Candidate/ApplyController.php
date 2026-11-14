<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Candidate;
use App\Models\Program;
use App\Models\Application;
use App\Models\ApplicationChoice;

class ApplyController extends Controller
{
    const MAX_PROGRAM_CHOICES = 3;

    // Session key where the selected program IDs (in priority order) are held
    // until the candidate finishes the whole flow and hits final Submit.
    const SESSION_KEY = 'application.selected_programs';

    /**
     * Step 1: Select Program
     * No DB writes here - selection lives in session until final submit.
     */
   public function index()
{
    $candidateLoginId = Auth::guard('web')->id();
    $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

    $currentSchedule = \App\Models\AdmissionSchedule::current();

    // Only treat an application as "already applied" if it belongs to the
    // currently open admission session.
    $latestApplication = $candidate
        ? Application::with('choices.program')
            ->where('candidate_id', $candidate->id)
            ->whereHas('choices.program', function ($q) use ($currentSchedule) {
                $q->where('session_id', $currentSchedule?->sessionid);
            })
            ->latest()
            ->first()
        : null;

    $alreadyapplied = $latestApplication !== null;
    $myApplications = $latestApplication ? $latestApplication->choices : collect();

    // Only show programs from the currently open session
    $programs = Program::where('status', 'open')
        ->when($currentSchedule, function ($q) use ($currentSchedule) {
            $q->where('session_id', $currentSchedule->sessionid);
        })
        ->orderBy('name')
        ->get();

    $groupedPrograms = $programs->groupBy('session_id');

    $sessions = \App\Models\Session::whereIn('id', $groupedPrograms->keys())->pluck('name', 'id');

    $sessionSelectedIds = session(self::SESSION_KEY, []);

    return view('candidate.apply', compact(
        'candidate',
        'groupedPrograms',
        'myApplications',
        'alreadyapplied',
        'latestApplication',
        'sessionSelectedIds',
        'sessions'
    ));
}

    /**
     * Store program choices in SESSION only (min 1, max 3).
     * No Candidate row, no Application row, no application_no here.
     */
   public function store(Request $request)
{
    $request->validate([
        'program_ids' => 'required|array|min:1|max:' . self::MAX_PROGRAM_CHOICES,
        'program_ids.*' => 'required|distinct|exists:program,id',
    ], [
        'program_ids.required' => 'Please select at least one program.',
        'program_ids.max' => 'You can select a maximum of ' . self::MAX_PROGRAM_CHOICES . ' programs.',
        'program_ids.*.distinct' => 'Each program can only be selected once.',
    ]);

    $candidateLoginId = Auth::guard('web')->id();
    $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

    $currentSchedule = \App\Models\AdmissionSchedule::current();

    // If already applied for the CURRENT open session, don't let them re-select.
       if ($candidate && Application::where('candidate_id', $candidate->id)
       ->whereHas('choices.program', function ($q) use ($currentSchedule) {
           $q->where('session_id', $currentSchedule?->sessionid);
       })
       ->exists()) {
       return redirect()->route('apply.status')->with('info', 'You have already submitted an application.');
   }

    session([self::SESSION_KEY => array_values($request->program_ids)]);

    return redirect()->route('profile.edit')->with('success', 'Program choices saved! Now complete your profile.');
}

    /**
     * My Applications - show latest SUBMITTED application with all choices
     */
   public function myApplications()
{
    $candidateLoginId = Auth::guard('web')->id();
    $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

    $currentSchedule = \App\Models\AdmissionSchedule::current();

    $latestApplication = null;
    $myApplications = collect();

    if ($candidate) {
        $latestApplication = Application::with('choices.program')
            ->where('candidate_id', $candidate->id)
            ->whereHas('choices.program', function ($q) use ($currentSchedule) {
                $q->where('session_id', $currentSchedule?->sessionid);
            })
            ->latest()
            ->first();

        if ($latestApplication) {
            $myApplications = $latestApplication->choices;
        }
    }

    return view('candidate.application-status', compact('latestApplication', 'myApplications'));
}

    /**
     * Download application form for specific choice
     */
    public function download($applicationChoiceId)
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

        $applicationChoice = ApplicationChoice::with('application', 'program')
            ->where('id', $applicationChoiceId)
            ->firstOrFail();

        // Verify ownership
        if (!$candidate || $applicationChoice->application->candidate_id !== $candidate->id) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $application = $applicationChoice->application;

        return view('candidate.application-print', compact('applicationChoice', 'application', 'candidate'));
    }
}