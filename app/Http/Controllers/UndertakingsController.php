<?php

namespace App\Http\Controllers;
use App\Models\AdmissionSchedule;
use App\Models\Candidate;
use App\Models\Program;
use App\Models\Application;
use App\Models\ApplicationChoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ProgramFeeStructure;


class UndertakingsController extends Controller
{
    // Same session key ApplyController writes selected program IDs to.
    const PROGRAMS_SESSION_KEY = 'application.selected_programs';

    // Session keys for the 3 undertaking checkboxes - NOT written to DB
    // until the candidate hits final Submit.
    const UNDERTAKING_SESSION_KEYS = [
        1 => 'application.undertaking_admission_accepted',
        2 => 'application.undertaking_refund_accepted',
        3 => 'application.undertaking_conduct_accepted',
    ];

    /**
     * Get the logged-in candidate's profile row, or null.
     */
    private function currentCandidate()
    {
        $candidateLoginId = Auth::guard('web')->id();
        return Candidate::where('candidate_login_id', $candidateLoginId)->first();
    }

    /**
     * Build the ordered [priority => n, name => ...] list from the
     * session-held program IDs (Program table is only read here, never
     * written to).
     */
    private function sessionProgramsList(): array
    {
        $programIds = session(self::PROGRAMS_SESSION_KEY, []);

        if (empty($programIds)) {
            return [];
        }

        $programs = Program::whereIn('id', $programIds)->get()->keyBy('id');

        $list = [];
        foreach (array_values($programIds) as $index => $programId) {
            $list[] = [
                'program_id' => $programId,
                'priority' => $index + 1,
                'name' => $programs->get($programId)->name ?? 'N/A',
            ];
        }

        return $list;
    }

    /**
     * Step 5: Profile Review - before undertakings.
     * Reads candidate profile from DB + selected programs from SESSION.
     */
    public function showProfileReview()
{
    $candidate = $this->currentCandidate();

    if (!$candidate) {
        return redirect()->route('profile.edit')->with('error', 'Please complete your profile first.');
    }

    $currentSchedule = AdmissionSchedule::current();

    // Only count an application as "already submitted" if it belongs to
    // the currently open admission session.
    $submittedApplication = Application::where('candidate_id', $candidate->id)
        ->whereIn('status', ['pending', 'approved'])
        ->whereHas('choices.program', function ($q) use ($currentSchedule) {
            $q->where('session_id', $currentSchedule?->sessionid);
        })
        ->latest('id')
        ->first();

    if ($submittedApplication) {
        $programs = $submittedApplication->choices()
            ->with('program')
            ->get()
            ->map(function ($choice) {
                return [
                    'program_id' => $choice->program_id,
                    'priority' => $choice->priority,
                    'name' => $choice->program->name ?? 'N/A',
                ];
            })
            ->toArray();
    } else {
        $programs = $this->sessionProgramsList();

        if (empty($programs)) {
            return redirect()->route('apply.index')->with('error', 'Please select your programs first.');
        }
    }

    $candidate->load('education', 'documents');

    return view('candidate.ProfileReview', [
        'candidate' => $candidate,
        'education' => $candidate->education,
        'documents' => $candidate->documents,
        'programs' => $programs,
        'isSubmitted' => (bool) $submittedApplication,
    ]);
}

    /**
     * Step 6: Show undertakings page.
     * Acceptance state (per checkbox) lives in SESSION until final submit.
     */
    public function showUndertakings()
    {
        $candidate = $this->currentCandidate();

        if (!$candidate) {
            return redirect()->route('profile.edit')->with('error', 'Please complete your profile first.');
        }

        $programs = $this->attachFeeBreakdown($this->sessionProgramsList());

        if (empty($programs)) {
            return redirect()->route('apply.index')->with('error', 'Please select your programs first.');
        }

        return view('candidate.Undertakings', [
            'application_no' => null, // not generated yet - only exists after final submit
            'candidate' => $candidate,
            'programs' => $programs,
            'undertaking1_accepted' => (bool) session(self::UNDERTAKING_SESSION_KEYS[1], false),
            'undertaking2_accepted' => (bool) session(self::UNDERTAKING_SESSION_KEYS[2], false),
            'undertaking3_accepted' => (bool) session(self::UNDERTAKING_SESSION_KEYS[3], false),
        ]);
    }

    /**
     * Accept an individual undertaking via AJAX - stored in SESSION only.
     */
    public function acceptUndertaking(Request $request)
    {
        $undertakingType = (int) $request->input('undertaking_type'); // 1, 2, or 3

        if (!array_key_exists($undertakingType, self::UNDERTAKING_SESSION_KEYS)) {
            return response()->json(['success' => false, 'message' => 'Invalid undertaking type.'], 400);
        }

        session([self::UNDERTAKING_SESSION_KEYS[$undertakingType] => true]);

        return response()->json([
            'success' => true,
            'message' => "Undertaking {$undertakingType} accepted.",
            'undertaking_type' => $undertakingType,
        ]);
    }

    /**
     * Step 7: FINAL SUBMIT.
     * This is the ONLY place in the whole flow where application_no,
     * the Application row, and the ApplicationChoice rows get created -
     * all in one shot, all inside a transaction.
     */
    public function submitApplication(Request $request)
    {
        $candidate = $this->currentCandidate();

        if (!$candidate) {
            return response()->json(['success' => false, 'message' => 'Please complete your profile first.'], 422);
        }

        // Already submitted? Don't create a duplicate.
       $currentSchedule = AdmissionSchedule::current();

$hasCurrentSessionApplication = Application::where('candidate_id', $candidate->id)
    ->whereIn('status', ['pending', 'approved'])
    ->whereHas('choices.program', function ($q) use ($currentSchedule) {
        $q->where('session_id', $currentSchedule?->sessionid);
    })
    ->exists();

if ($hasCurrentSessionApplication) {
            session()->forget([self::PROGRAMS_SESSION_KEY, ...array_values(self::UNDERTAKING_SESSION_KEYS)]);
            return response()->json(['success' => true, 'message' => 'Application already submitted.']);
        }

        $programIds = session(self::PROGRAMS_SESSION_KEY, []);

        if (empty($programIds)) {
            return response()->json(['success' => false, 'message' => 'No programs selected. Please start over.'], 422);
        }

        $undertaking1 = (bool) session(self::UNDERTAKING_SESSION_KEYS[1], false);
        $undertaking2 = (bool) session(self::UNDERTAKING_SESSION_KEYS[2], false);
        $undertaking3 = (bool) session(self::UNDERTAKING_SESSION_KEYS[3], false);

        if (!$undertaking1 || !$undertaking2 || !$undertaking3) {
            return response()->json(['success' => false, 'message' => 'Please accept all undertakings before submitting.'], 422);
        }

        try {
            $application = DB::transaction(function () use ($candidate, $programIds) {
                // Generate application_no here, at the very last moment,
                // right before the row is actually inserted.
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
                    'undertaking_admission_accepted' => 1,
                    'undertaking_refund_accepted' => 1,
                    'undertaking_conduct_accepted' => 1,
                    'undertakings_completed_at' => Carbon::now(),
                ]);

                foreach (array_values($programIds) as $index => $programId) {
                    ApplicationChoice::create([
                        'application_id' => $application->id,
                        'program_id' => $programId,
                        'priority' => $index + 1,
                        'admin_status' => 'pending',
                    ]);
                }

                return $application;
            });

            // Clean slate - nothing left in session for this application.
            session()->forget([self::PROGRAMS_SESSION_KEY, ...array_values(self::UNDERTAKING_SESSION_KEYS)]);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'application_no' => $application->application_no,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error submitting application: ' . $e->getMessage()], 500);
        }
    }
    private function attachFeeBreakdown(array $programs): array
    {
        // Days-from-start slabs: [fromDay, toDay(or null = open-ended), percent]
        $slabs = [
            ['from' => 1,  'to' => 10, 'percent' => 100],
            ['from' => 11, 'to' => 15, 'percent' => 80],
            ['from' => 16, 'to' => 20, 'percent' => 60],
            ['from' => 21, 'to' => 30, 'percent' => 50],
            ['from' => 31, 'to' => null, 'percent' => 0],
        ];
 
        return array_map(function ($program) use ($slabs) {
            $feeRow = ProgramFeeStructure::where('program_id', $program['program_id'])
                ->where('semester', 1)
                ->first();
 
            $tuitionFee = $feeRow->amount ?? null;
            $admissionFee = $feeRow->admission_fee ?? null;
 
            // Get the semester start date via the program's session_id
            $programRow = Program::find($program['program_id']);
            $sessionStart = null;
 
            if ($programRow && $programRow->session_id) {
                $sessionStartRaw = DB::table('session')
                    ->where('id', $programRow->session_id)
                    ->value('start_date');
 
                if ($sessionStartRaw) {
                    $sessionStart = Carbon::parse($sessionStartRaw);
                }
            }
 
            $program['tuition_fee'] = $tuitionFee;
            $program['admission_fee'] = $admissionFee;
            $program['total_fee'] = ($tuitionFee !== null && $admissionFee !== null)
                ? $tuitionFee + $admissionFee
                : null;
            $program['session_start'] = $sessionStart;
 
            $program['refund_breakdown'] = array_map(function ($slab) use ($tuitionFee, $sessionStart) {
                $dateFrom = null;
                $dateTo = null;
 
                if ($sessionStart) {
                    $dateFrom = $sessionStart->copy()->addDays($slab['from'] - 1);
                    $dateTo = $slab['to'] !== null
                        ? $sessionStart->copy()->addDays($slab['to'] - 1)
                        : null;
                }
 
                return [
                    'from_day' => $slab['from'],
                    'to_day' => $slab['to'],
                    'percent' => $slab['percent'],
                    'amount' => $tuitionFee !== null ? round($tuitionFee * $slab['percent'] / 100) : null,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ];
            }, $slabs);
 
            return $program;
        }, $programs);
    }
 
}
