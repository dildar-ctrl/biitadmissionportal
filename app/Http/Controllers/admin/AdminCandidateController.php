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

        // Base list is candidate_login — every account that ever signed up,
        // whether or not they went on to complete their profile.
        $candidates = CandidateLogin::with([
                'candidate.education.majorSubjects',
                'candidate.education.marksType',
                'candidate.education.boardUniversity',
                'candidate.documents.documentType',
            ])
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

        return view('admin.candidates', compact('candidates', 'search'));
    }
}