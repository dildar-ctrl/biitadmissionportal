<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Candidate\CandidateProfileController;
use App\Http\Controllers\Candidate\DocumentController;
use App\Http\Controllers\Candidate\ApplyController;
use App\Http\Controllers\Candidate\CandidateDashboardController;
use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Admin\AdminCandidateController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ProgramPatternController;
use App\Http\Controllers\Admin\TestGenerateController;
use App\Http\Controllers\Admin\AdminRollSlipController;
use App\Http\Controllers\Admin\TestVersionController;
use App\Http\Controllers\TestAttemptController;
use App\Http\Controllers\TestLoginController;
use App\Http\Controllers\Admin\AdminResultController;
use App\Http\Controllers\Candidate\CandidateResultController;


/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/

// ----- Root: always open login page first -----
Route::get('/', function () {
    return redirect()->route('login');
});

// ----- Login (guest only) -----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // ----- Signup (candidates only) -----
    Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
});

// ----- Logout (any logged in user, admin or candidate) -----
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Test Login + Test Taking
|--------------------------------------------------------------------------
| Intentionally NOT wrapped in auth:web or auth:admin — this is a
| separate, one-purpose session (Roll No + Test Password), checked
| manually inside TestLoginController / TestAttemptController.
*/

Route::get('/test-login', [TestLoginController::class, 'showLoginForm'])->name('test.login');
Route::post('/test-login', [TestLoginController::class, 'login'])->name('test.login.submit');
Route::post('/test-logout', [TestLoginController::class, 'logout'])->name('test.logout');

Route::get('/test/attempt', [TestAttemptController::class, 'show'])->name('test.attempt');
Route::post('/test/attempt/submit', [TestAttemptController::class, 'submit'])->name('test.submit');


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

    // Roll Slip + Result (candidate's own, ownership-checked inside the controller)
    Route::get('/roll-slip/{application}', [CandidateResultController::class, 'rollSlip'])->name('candidate.rollslip.show');
    Route::get('/result/{application}', [CandidateResultController::class, 'result'])->name('candidate.result.show');

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
    Route::post('/applications/{application}/reject', [AdminApplicationController::class, 'reject'])->name('admin.applications.reject');

    // Roll Slip generation
    Route::post('/applications/{application}/generate-roll-slip', [AdminRollSlipController::class, 'generate'])->name('admin.rollslip.generate');
    Route::get('/applications/{application}/roll-slip', [AdminRollSlipController::class, 'show'])->name('admin.rollslip.show');
    Route::post('/applications/generate-all-roll-slips', [AdminRollSlipController::class, 'generateAll'])->name('admin.rollslip.generateAll');

    // Results
    Route::get('/results', [AdminResultController::class, 'index'])->name('admin.results.index');
    Route::post('/results/{id}/publish', [AdminResultController::class, 'publish'])->name('admin.results.publish');
    Route::post('/results/publish-all', [AdminResultController::class, 'publishAll'])->name('admin.results.publishAll');

    // Questions
    Route::get('/question', [QuestionController::class, 'index'])->name('admin.question.index');
    Route::get('/question/create', [QuestionController::class, 'create'])->name('admin.question.create');
    Route::get('/question/categories/{degreeLevelId}', [QuestionController::class, 'getCategories'])->name('admin.question.categories');
    Route::post('/question/store', [QuestionController::class, 'store'])->name('admin.question.store');
    Route::get('/question/{id}/edit', [QuestionController::class, 'edit'])->name('admin.question.edit');
    Route::put('/question/{id}', [QuestionController::class, 'update'])->name('admin.question.update');
    Route::delete('/question/{id}', [QuestionController::class, 'destroy'])->name('admin.question.destroy');

    // Program Pattern
    Route::get('/pattern', [ProgramPatternController::class, 'index'])->name('admin.pattern.index');
    Route::get('/pattern/create', [ProgramPatternController::class, 'create'])->name('admin.pattern.create');
    Route::get('/pattern/programs/{degreeLevelId}', [ProgramPatternController::class, 'getPrograms'])->name('admin.pattern.programs');
    Route::get('/pattern/categories/{degreeLevelId}', [ProgramPatternController::class, 'getCategoriesWithCounts'])->name('admin.pattern.categories');
    Route::post('/pattern/store', [ProgramPatternController::class, 'store'])->name('admin.pattern.store');

    // Tests — literal routes MUST come before /test/{id}
    Route::get('/test', [TestGenerateController::class, 'index'])->name('admin.test.index');
    Route::get('/test/create', [TestGenerateController::class, 'create'])->name('admin.test.create');
    Route::get('/test/programs/{degreeLevelId}', [TestGenerateController::class, 'getPrograms'])->name('admin.test.programs');
    Route::get('/test/pattern-preview/{programId}', [TestGenerateController::class, 'getPatternPreview'])->name('admin.test.pattern-preview');
    Route::post('/test/store', [TestGenerateController::class, 'store'])->name('admin.test.store');
    Route::post('/test/{id}/publish', [TestGenerateController::class, 'publish'])->name('admin.test.publish');
    Route::get('/test/{id}/edit', [TestGenerateController::class, 'edit'])->name('admin.test.edit');
    Route::put('/test/{id}', [TestGenerateController::class, 'update'])->name('admin.test.update');
    Route::delete('/test/{id}', [TestGenerateController::class, 'destroy'])->name('admin.test.destroy');
    Route::get('/test/{id}', [TestGenerateController::class, 'show'])->name('admin.test.show'); // must stay LAST

    // Test Versions (per-test, for latecomers)
    Route::post('/test/{testId}/versions', [TestVersionController::class, 'store'])->name('admin.test.versions.store');
    Route::delete('/test/{testId}/versions/{versionId}', [TestVersionController::class, 'destroy'])->name('admin.test.versions.destroy');

});
 Route::get('/document/{document}/file', [DocumentController::class, 'showFile'])->name('document.file');