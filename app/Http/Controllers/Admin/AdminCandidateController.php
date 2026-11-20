<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CandidateLogin;

class AdminCandidateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sessionId = $request->query('session_id');

        $candidates = CandidateLogin::with([
                'candidate.education',
                'candidate.documents.documentType',
            ])
            ->when($sessionId, function ($query) use ($sessionId) {
                $query->whereHas('candidate.applications.choices.program', function ($q) use ($sessionId) {
                    $q->where('session_id', $sessionId);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('fname', 'like', "%{$search}%")
                        ->orWhere('lname', 'like', "%{$search}%")
                        ->orWhere('cnic', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile_no', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.candidates', compact('candidates', 'search', 'sessionId'));
    }
}