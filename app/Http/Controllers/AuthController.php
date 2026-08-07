<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\CandidateLogin;
use App\Models\AdminUser;
use App\Models\Candidate;
use App\Models\Application;

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
            $hasApplied = $candidateProfile
                ? Application::where('candidate_id', $candidateProfile->id)->exists()
                : false;

            if ($hasApplied) {
                return redirect()->route('apply.status')->with('success', 'Welcome back, ' . $candidate->fname . '!');
            }

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

    public function signup(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'cnic' => 'required|string|max:20|unique:candidatelogin,cnic',
            'mobile_no' => 'required|string|max:20',
            'email' => 'required|email|max:150|unique:candidatelogin,email',
            'password' => 'required|string|min:6|confirmed', // expects password_confirmation field
        ]);

        CandidateLogin::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'cnic' => $request->cnic,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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