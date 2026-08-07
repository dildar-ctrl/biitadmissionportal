<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Candidate;
use App\Models\DocumentType;
use App\Models\Document;

class CandidateDashboardController extends Controller
{
    public function index()
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();

        // ---- Step 2: Profile — complete only when EVERY required field is filled ----
        $requiredProfileFields = [
            'fname', 'lname', 'cnic', 'dob', 'postal_address', 'permanent_address',
            'district', 'gender', 'mobile_no', 'religion', 'marital_status',
            'province_of_domicile', 'district_of_domicile',
        ];

        $profileComplete = false;
        if ($candidate) {
            $profileComplete = true;
            foreach ($requiredProfileFields as $field) {
                if (empty($candidate->{$field})) {
                    $profileComplete = false;
                    break;
                }
            }
        }

        // ---- Step 3: Documents — complete only when EVERY document type has been uploaded ----
        $documentsComplete = false;
        if ($candidate) {
            $requiredDocTypeIds = DocumentType::pluck('id');
            $uploadedDocTypeIds = Document::where('candidate_id', $candidate->id)->pluck('documenttype_id');

            // true only if no required document type is missing from what's uploaded
            $documentsComplete = $requiredDocTypeIds->diff($uploadedDocTypeIds)->isEmpty() && $requiredDocTypeIds->isNotEmpty();
        }

        return view('dashboard', compact('profileComplete', 'documentsComplete'));
    }
}