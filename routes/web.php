<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\Candidate\CandidateProfileController;
use App\Http\Controllers\Candidate\DocumentController;
use App\Http\Controllers\Candidate\ApplyController;
use App\Http\Controllers\Candidate\CandidateDashboardController;
use App\Http\Controllers\admin\AdminApplicationController;
use App\Http\Controllers\admin\AdminCandidateController;
use App\Http\Controllers\UndertakingsController;
use App\Http\Controllers\ForgotPasswordController;


/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/

// ----- Root: always open login page first -----
// ----- Root: public landing page -----
Route::get('/', function () {
    return view('landing');
})->name('landing');

// ----- Login (guest only) -----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // ----- Signup (candidates only) -----
    Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
   Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.forgot');
Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyIdentity'])
    ->middleware('throttle:6,1')->name('password.verify');
Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])
    ->middleware('throttle:10,1')->name('password.otp.verify');
Route::post('/forgot-password/otp/resend', [ForgotPasswordController::class, 'resendOtp'])
    ->middleware('throttle:6,1')->name('password.otp.resend');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'reset'])->name('password.reset');});

// ----- Logout (any logged in user, admin or candidate) -----
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| Candidate Protected Routes (auth:web)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:web')->group(function () {

    Route::get('/dashboard', [CandidateDashboardController::class, 'index'])->name('dashboard');

    // Step 2 & 4: Profile
    Route::get('/profile', [CandidateProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [CandidateProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/view', [CandidateProfileController::class, 'view'])->name('profile.view');

    // Step 3: Documents
    Route::get('/document', [DocumentController::class, 'edit'])->name('document.edit');
    Route::post('/document', [DocumentController::class, 'store'])->name('document.store');
   

    // Step 5: Apply & Submit
    Route::get('/apply', [ApplyController::class, 'index'])->name('apply.index');
    Route::post('/apply', [ApplyController::class, 'store'])->name('apply.store');
    Route::get('/apply/my-applications', [ApplyController::class, 'myApplications'])->name('apply.status');
    Route::get('/apply/download/{id}', [ApplyController::class, 'download'])->name('apply.download');

});


/*
|--------------------------------------------------------------------------
| Admin Protected Routes (auth:admin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:admin')->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Candidates
    Route::get('/candidates', [AdminCandidateController::class, 'index'])->name('admin.candidates.index');

    // Applications management (view, approve, reject)
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('admin.applications.index');
    Route::post('/applications/{application}/approve', [AdminApplicationController::class, 'approve'])->name('admin.applications.approve');
    Route::post('/applications/offline-create', [AdminApplicationController::class, 'storeOffline'])->name('admin.applications.offline.store');
    Route::post('/applications/{application}/reject', [AdminApplicationController::class, 'reject'])->name('admin.applications.reject');
    Route::get('/applications/{application}/print', [AdminApplicationController::class, 'print'])->name('admin.applications.print');
});
 Route::get('/document/{document}/file', [DocumentController::class, 'showFile'])->name('document.file');

 Route::middleware(['auth'])->group(function () {
    // Step 5: Profile Review - selected programs (session) + candidate profile, before undertakings
    Route::get('/profile-review', [UndertakingsController::class, 'showProfileReview'])
        ->name('profile.review');
 
    // Step 6: Show Undertakings (step-by-step)
    Route::get('/undertakings', [UndertakingsController::class, 'showUndertakings'])
        ->name('undertakings.show');
 
    // Accept individual undertaking (AJAX) - stored in session until final submit
    Route::post('/undertakings/accept', [UndertakingsController::class, 'acceptUndertaking'])
        ->name('undertakings.accept');
 
    // Step 7: Final Submit - THIS is where application_no + Application row + ApplicationChoice rows get created
    Route::post('/undertakings/submit', [UndertakingsController::class, 'submitApplication'])
        ->name('undertakings.submit');
});
 Route::get('/admissions-closed', function () {
    return view('candidate.admissions-closed');
})->name('admissions.closed');