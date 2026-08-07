<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // NOTE: table names below assume the underscore naming from the original
        // schema (candidate, test, test_attempt). If your actual database uses
        // the no-underscore names (candidate, test, testattempt), update the
        // table names in this method to match.

        $totalCandidates = DB::table('candidate')->count();
        $totalTests = DB::table('test')->count();
        $totalAttempts = DB::table('testattempt')->count();

        $passCount = DB::table('testattempt')->where('status', 'pass')->count();
        $failCount = DB::table('testattempt')->where('status', 'fail')->count();
        $pendingCount = DB::table('testattempt')->where('status', 'pending')->count();

        $passPct = $totalAttempts ? round($passCount / $totalAttempts * 100) : 0;
        $failPct = $totalAttempts ? round($failCount / $totalAttempts * 100) : 0;
        $pendingPct = $totalAttempts ? round($pendingCount / $totalAttempts * 100) : 0;

        // ---- Application status counts (candidate applications to programs) ----
        $totalApplications = DB::table('application')->count();
        $approvedApplications = DB::table('application')->where('status', 'approved')->count();
        $pendingApplications = DB::table('application')->where('status', 'pending')->count();
        $rejectedApplications = DB::table('application')->where('status', 'rejected')->count();

        $recentAttempts = DB::table('testattempt as ta')
            ->join('candidate as c', 'c.id', '=', 'ta.candidate_id')
            ->join('test as t', 't.id', '=', 'ta.test_id')
            ->select(
                DB::raw("CONCAT(c.fname, ' ', c.lname) as candidate_name"),
                't.title as test_title',
                'ta.attempt_date as date'
            )
            ->orderByDesc('ta.attempt_date')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalCandidates',
            'totalTests',
            'totalAttempts',
            'passCount',
            'failCount',
            'pendingCount',
            'passPct',
            'failPct',
            'pendingPct',
            'totalApplications',
            'approvedApplications',
            'pendingApplications',
            'rejectedApplications',
            'recentAttempts'
        ));
    }
}