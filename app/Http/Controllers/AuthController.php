<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\CandidateLogin;
use App\Models\AdminUser;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\AdmissionSchedule;

class AuthController extends Controller
{
    // ===================== LOGIN (shared by admin + candidate) =====================

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($request->has('redirect')) {
            session(['url.intended' => $request->redirect]);
        }

        // 1. Check admin_users table first
        $admin = AdminUser::where('email', $request->email)->first();

        if ($admin) {
            if (!Hash::check($request->password, $admin->password)) {
                return redirect()->route('login')->with('error', 'Incorrect password. Please try again.');
            }

            if ($admin->status !== 'active') {
                return redirect()->route('login')->with('error', 'This admin account is inactive. Contact super admin.');
            }

            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $admin->fname . '!');
        }

        // 2. Check candidate_login table
        $candidate = CandidateLogin::where('email', $request->email)->first();

        if ($candidate) {
            if (!Hash::check($request->password, $candidate->password)) {
                return redirect()->route('login')->with('error', 'Incorrect password. Please try again.');
            }

            Auth::guard('web')->login($candidate);
            $request->session()->regenerate();

            $intendedUrl = session('url.intended');
            if ($intendedUrl) {
                session()->forget('url.intended');
                return redirect()->to($intendedUrl)->with('success', 'Welcome back, ' . $candidate->fname . '!');
            }

            // If this candidate has already applied to at least one program,
            // take them straight to "My Applications" instead of the plain
            // dashboard — that's the more useful landing page once they've
            // started applying. New candidates (no applications yet) still
            // land on the normal dashboard as before.
            $candidateProfile = Candidate::where('candidate_login_id', $candidate->id)->first();

           $candidateProfile = Candidate::where('candidate_login_id', $candidate->id)->first();
$currentSchedule = AdmissionSchedule::current();

if ($candidateProfile) {
    $latestApplication = Application::where('candidate_id', $candidateProfile->id)
        ->latest('id')
        ->first();

    if ($latestApplication) {
        $firstChoice = $latestApplication->choices()->with('program')->first();
        $applicationSessionId = optional($firstChoice)->program->session_id ?? null;

        // Application belongs to the currently open session → normal status page
        if ($currentSchedule && $applicationSessionId == $currentSchedule->sessionid) {
            return redirect()->route('apply.status')->with('success', 'Welcome back, ' . $candidate->fname . '!');
        }

        // No admission window open right now
        if (!$currentSchedule) {
            return redirect()->route('admissions.closed')->with('info', 'Admissions are currently closed.');
        }

        // A new session is open, but this application was for an old/closed session
        return redirect()->route('apply.index')->with('info', 'A new admission session is open. Please select your program(s).');
    }

    // Candidate never applied yet
    if (!$currentSchedule) {
        return redirect()->route('admissions.closed')->with('info', 'Admissions are currently closed.');
    }
}

return redirect()->route('dashboard')->with('success', 'Welcome back, ' . $candidate->fname . '!');
            return redirect()->route('dashboard')->with('success', 'Welcome back, ' . $candidate->fname . '!');
            }

        // 3. Neither table had this email
        return redirect()->route('login')->with('error', 'Account not found.');
    }

    // ===================== SIGNUP (candidates only) =====================

    public function showSignupForm()
    {
        return view('signup');
    }

    public function signup(SignupRequest $request)
    {
        // Validation rules, messages and input cleanup (CNIC dashes,
        // mobile format, lowercase email) live in SignupRequest.
        $data = $request->validated();

        CandidateLogin::create([
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'cnic' => $data['cnic'],
            'mobile_no' => $data['mobile_no'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! Please log in.');
    }

    // ===================== LOGOUT =====================

    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully!');
    }
    
}