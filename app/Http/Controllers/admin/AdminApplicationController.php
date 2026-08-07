<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;

class AdminApplicationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status'); // approved / pending / rejected / null (all)

        $applications = Application::with([
                'candidate.education.marksType',
                'candidate.education.boardUniversity',
                'candidate.documents.documentType',
                'program',
                'rollSlip',
            ])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('applied_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.applications', compact('applications', 'status'));
    }

    // Admin approves a pending application
    public function approve(Application $application)
    {
        $application->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Application ' . $application->application_no . ' approved.');
    }

    // Admin rejects a pending application
    public function reject(Application $application)
    {
        $application->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Application ' . $application->application_no . ' rejected.');
    }
}