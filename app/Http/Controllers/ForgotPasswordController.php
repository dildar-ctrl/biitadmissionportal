<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\CandidateLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    // Step 1 passed: OTP sent to this candidate (not yet proven)
    const PENDING_KEY = 'password_reset_pending_id';
    // Step 2 passed: OTP proven, password may be changed
    const RESET_SESSION_KEY = 'password_reset_candidate_id';
    const VERIFIED_AT_KEY = 'password_reset_verified_at';

    const OTP_EXPIRY_MINUTES = 10;
    const MAX_ATTEMPTS = 5;
    const RESEND_COOLDOWN = 60;          // seconds
    const RESET_WINDOW_MINUTES = 15;     // time allowed to set the password after OTP verified

    public function showForm()
    {
        return view('forgot-password');
    }

    // Step 1: email + CNIC -> send OTP
    public function verifyIdentity(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'cnic' => 'required|string',
        ]);

        session()->forget([self::PENDING_KEY, self::RESET_SESSION_KEY, self::VERIFIED_AT_KEY]);

        $candidate = CandidateLogin::where('email', strtolower(trim($request->email)))
            ->where('cnic', trim($request->cnic))
            ->first();

        if ($candidate) {
            session([self::PENDING_KEY => $candidate->id]);

            if (!$this->issueOtp($candidate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'We could not send the email right now. Please try again later.',
                ], 500);
            }
        }

        // Same response whether or not an account matched, so nobody can
        // use this form to find out which emails/CNICs are registered.
        return response()->json([
            'success' => true,
            'message' => 'If the details match an account, a 6-digit code has been sent to the registered email.',
            'cooldown' => self::RESEND_COOLDOWN,
        ]);
    }

    // Step 2: check the OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $invalid = response()->json([
            'success' => false,
            'message' => 'Invalid or expired code.',
        ], 422);

        $candidateId = session(self::PENDING_KEY);
        if (!$candidateId) {
            return $invalid;
        }

        $row = DB::table('passwordresetotps')->where('candidateloginid', $candidateId)->first();

        if (!$row
            || now()->greaterThan(Carbon::parse($row->expiresat))
            || $row->attempts >= self::MAX_ATTEMPTS) {
            DB::table('passwordresetotps')->where('candidateloginid', $candidateId)->delete();
            return $invalid;
        }

        if (!Hash::check($request->otp, $row->otphash)) {
            $attempts = $row->attempts + 1;

            if ($attempts >= self::MAX_ATTEMPTS) {
                DB::table('passwordresetotps')->where('id', $row->id)->delete();
            } else {
                DB::table('passwordresetotps')->where('id', $row->id)->update(['attempts' => $attempts]);
            }

            return $invalid;
        }

        // Correct code: one-time use
        DB::table('passwordresetotps')->where('candidateloginid', $candidateId)->delete();

        session()->forget(self::PENDING_KEY);
        session([
            self::RESET_SESSION_KEY => $candidateId,
            self::VERIFIED_AT_KEY => now()->timestamp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Code verified. Please set your new password.',
        ]);
    }

    // Resend OTP (60s cooldown)
    public function resendOtp()
    {
        $candidateId = session(self::PENDING_KEY);

        $ok = response()->json([
            'success' => true,
            'message' => 'If the details match an account, a new code has been sent.',
            'cooldown' => self::RESEND_COOLDOWN,
        ]);

        if (!$candidateId) {
            return $ok;
        }

        $row = DB::table('passwordresetotps')->where('candidateloginid', $candidateId)->first();

        if ($row) {
            $wait = self::RESEND_COOLDOWN - Carbon::parse($row->lastsentat)->diffInSeconds(now());
            if ($wait > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait ' . $wait . ' seconds before requesting a new code.',
                    'cooldown' => $wait,
                ], 422);
            }
        }

        $candidate = CandidateLogin::find($candidateId);
        if ($candidate && !$this->issueOtp($candidate)) {
            return response()->json([
                'success' => false,
                'message' => 'We could not send the email right now. Please try again later.',
            ], 500);
        }

        return $ok;
    }

    // Step 3: set new password (only after OTP verified)
    public function reset(Request $request)
    {
        $candidateId = session(self::RESET_SESSION_KEY);
        $verifiedAt = session(self::VERIFIED_AT_KEY);

        if (!$candidateId || !$verifiedAt
            || now()->timestamp - $verifiedAt > self::RESET_WINDOW_MINUTES * 60) {
            session()->forget([self::RESET_SESSION_KEY, self::VERIFIED_AT_KEY]);
            return response()->json([
                'success' => false,
                'message' => 'Your session expired. Please start again.',
            ], 422);
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/[A-Za-z]/', 'regex:/[0-9]/'],
        ], [
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain at least one letter and one number.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $candidate = CandidateLogin::find($candidateId);

        if (!$candidate) {
            session()->forget([self::RESET_SESSION_KEY, self::VERIFIED_AT_KEY]);
            return response()->json([
                'success' => false,
                'message' => 'Account not found. Please start again.',
            ], 422);
        }

        $candidate->password = Hash::make($request->password);
        $candidate->save();

        session()->forget([self::RESET_SESSION_KEY, self::VERIFIED_AT_KEY]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully! You can now log in.',
        ]);
    }

    // Create a fresh OTP (old one is invalidated) and email it
    private function issueOtp(CandidateLogin $candidate): bool
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('passwordresetotps')->where('candidateloginid', $candidate->id)->delete();

        DB::table('passwordresetotps')->insert([
            'candidateloginid' => $candidate->id,
            'otphash' => Hash::make($otp),
            'attempts' => 0,
            'expiresat' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'lastsentat' => now(),
            'createdat' => now(),
        ]);

        try {
            Mail::to($candidate->email)->send(
                new PasswordResetOtpMail($otp, $candidate->fname, self::OTP_EXPIRY_MINUTES)
            );
        } catch (\Throwable $e) {
            Log::error('Password reset OTP email failed: ' . $e->getMessage());
            DB::table('passwordresetotps')->where('candidateloginid', $candidate->id)->delete();
            return false;
        }

        return true;
    }
}