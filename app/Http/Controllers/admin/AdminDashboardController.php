<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // ---- All sessions for the dropdown ----
        $sessions = DB::table('session')->orderByDesc('start_date')->get();
        $selectedSessionId = $request->get('session_id'); // null/empty = "All Sessions"

        // Reusable filter: restrict applications to ones that have at least
        // one choice for a program belonging to the selected session.
        $applyingSessionFilter = function ($query) use ($selectedSessionId) {
            if ($selectedSessionId) {
                $query->whereIn('applications.id', function ($sub) use ($selectedSessionId) {
                    $sub->select('applicationchoices.application_id')
                        ->from('applicationchoices')
                        ->join('program', 'program.id', '=', 'applicationchoices.program_id')
                        ->where('program.session_id', $selectedSessionId);
                });
            }
        };

        // ---- Total Candidates ----
        // If a session is selected, count only candidates who have an
        // application for that session; otherwise count everyone.
        if ($selectedSessionId) {
            $totalCandidates = DB::table('candidate')
                ->whereIn('candidate.id', function ($sub) use ($selectedSessionId) {
                    $sub->select('applications.candidate_id')
                        ->from('applications')
                        ->join('applicationchoices', 'applicationchoices.application_id', '=', 'applications.id')
                        ->join('program', 'program.id', '=', 'applicationchoices.program_id')
                        ->where('program.session_id', $selectedSessionId);
                })
                ->count();
        } else {
            $totalCandidates = DB::table('candidate')->count();
        }

        // ---- Application status counts ----
        $totalApplications = DB::table('applications')->where($applyingSessionFilter)->count();
        $approvedApplications = DB::table('applications')->where('status', 'approved')->where($applyingSessionFilter)->count();
        $pendingApplications = DB::table('applications')->where('status', 'pending')->where($applyingSessionFilter)->count();
        $rejectedApplications = DB::table('applications')->where('status', 'rejected')->where($applyingSessionFilter)->count();

        // ---- Programs currently open for admission ----
        $totalOpenPrograms = DB::table('program')
            ->where('status', 'open')
            ->when($selectedSessionId, fn($q) => $q->where('session_id', $selectedSessionId))
            ->count();

        // ---- Applications growth vs last month ----
        $applicationsThisMonth = DB::table('applications')
            ->whereYear('applied_at', now()->year)
            ->whereMonth('applied_at', now()->month)
            ->where($applyingSessionFilter)
            ->count();

        $applicationsLastMonth = DB::table('applications')
            ->whereYear('applied_at', now()->subMonth()->year)
            ->whereMonth('applied_at', now()->subMonth()->month)
            ->where($applyingSessionFilter)
            ->count();

        $applicationsGrowth = $applicationsLastMonth > 0
            ? round((($applicationsThisMonth - $applicationsLastMonth) / $applicationsLastMonth) * 100, 1)
            : ($applicationsThisMonth > 0 ? 100 : 0);

        // ---- Monthly application trend for current year (line chart) ----
        $monthlyLabels = [];
        $monthlyApplications = [];
        for ($month = 1; $month <= now()->month; $month++) {
            $monthlyLabels[] = Carbon::create()->month($month)->format('M');
            $monthlyApplications[] = DB::table('applications')
                ->whereYear('applied_at', now()->year)
                ->whereMonth('applied_at', $month)
                ->where($applyingSessionFilter)
                ->count();
        }

        // ---- Applications by program ----
        $applicationsByProgram = DB::table('applicationchoices')
            ->join('program', 'program.id', '=', 'applicationchoices.program_id')
            ->join('applications', 'applications.id', '=', 'applicationchoices.application_id')
            ->where('applicationchoices.admin_status', 'approved')
            ->when($selectedSessionId, fn($q) => $q->where('program.session_id', $selectedSessionId))
            ->select('program.name as program_name', DB::raw('count(*) as total'))
            ->groupBy('program.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ---- Latest applications ----
        $recentApplications = DB::table('applications')
            ->join('candidate', 'candidate.id', '=', 'applications.candidate_id')
            ->join('candidatelogin', 'candidatelogin.id', '=', 'candidate.candidate_login_id')
            ->where($applyingSessionFilter)
            ->select(
                'applications.id',
                'applications.application_no',
                'applications.status',
                'applications.applied_at',
                'candidatelogin.email'
            )
            ->orderByDesc('applications.id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'sessions',
            'selectedSessionId',
            'totalCandidates',
            'totalApplications',
            'approvedApplications',
            'pendingApplications',
            'rejectedApplications',
            'totalOpenPrograms',
            'applicationsGrowth',
            'monthlyLabels',
            'monthlyApplications',
            'applicationsByProgram',
            'recentApplications'
        ));
    }
}