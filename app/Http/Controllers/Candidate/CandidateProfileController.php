<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Candidate;
use App\Models\Education;
use App\Models\MarksType;
use App\Models\BoardUniversity;

class CandidateProfileController extends Controller
{
    // Step 2: Profile create/update form
    public function edit()
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidateLogin = Auth::guard('web')->user();

        $candidate = Candidate::where('candidate_login_id', $candidateLoginId)->first();
        $educations = collect();

        if ($candidate) {
            $educations = Education::where('candidate_id', $candidate->id)
                ->orderBy('id')
                ->get();
        }

        $marksTypes = MarksType::all();
        $boardUniversities = BoardUniversity::orderBy('title')->get();

        return view('candidate.profile', compact(
            'candidate',
            'candidateLogin',
            'educations',
            'marksTypes',
            'boardUniversities'
        ));
    }

    // Save / update profile + education together
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // ---- Personal information ----
            'fname' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'lname' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'email' => ['required', 'email', 'max:150'],
            'cnic' => ['required', 'regex:/^\d{5}-\d{7}-\d{1}$/'],
            'dob' => ['required', 'date', 'before:today'],
            'postal_address' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\.\,\-\/]+$/'],
            'permanent_address' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\.\,\-\/]+$/'],
            'district' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'mobile_no' => ['required', 'regex:/^03\d{9}$/'],
            'alternative_mobile_no' => ['nullable', 'regex:/^03\d{9}$/', 'different:mobile_no'],
            'religion' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'marital_status' => ['required', 'in:Single,Married'],
            'province_of_domicile' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'district_of_domicile' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],

            // ---- Father's details ----
            'father_name' => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'father_cnic' => ['required', 'regex:/^\d{5}-\d{7}-\d{1}$/', 'different:cnic'],
            'father_occupation' => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z0-9\s\.\-\/]+$/'],
            'father_mobile_no' => ['required', 'regex:/^03\d{9}$/'],
            'father_email' => ['required', 'email', 'max:150'],

            // ---- Education (repeatable array) ----
            'education' => ['required', 'array', 'min:1'],
            'education.*.degree' => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z0-9\s\.\-\/]+$/'],
            'education.*.major_subject' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\,\.\-\/]+$/'],
            'education.*.date_of_passing' => ['required', 'date', 'before_or_equal:today'],
            'education.*.marks_type_id' => ['required', 'exists:markstype,id'],
            'education.*.obtained_marks' => ['required', 'numeric', 'min:0'],
            'education.*.total_marks' => ['required', 'numeric', 'min:1'],
            'education.*.board_university_id' => ['required', 'exists:boarduniversity,id'],
        ], [
            'fname.regex' => 'First name may only contain letters.',
            'lname.regex' => 'Last name may only contain letters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'cnic.regex' => 'CNIC must be in the format 12345-1234567-1.',
            'mobile_no.regex' => 'Mobile number must be in the format 03XXXXXXXXX.',
            'alternative_mobile_no.regex' => 'Alternative mobile number must be in the format 03XXXXXXXXX.',
            'alternative_mobile_no.different' => 'Alternative mobile number must be different from your mobile number.',
            'district.regex' => 'District may only contain letters.',
            'religion.regex' => 'Religion may only contain letters.',
            'province_of_domicile.regex' => 'Province of domicile may only contain letters.',
            'district_of_domicile.regex' => 'District of domicile may only contain letters.',
            'father_name.regex' => "Father's name may only contain letters.",
            'father_cnic.regex' => "Father's CNIC must be in the format 12345-1234567-1.",
            'father_cnic.different' => "Father's CNIC cannot be the same as your own CNIC.",
            'father_mobile_no.regex' => "Father's mobile number must be in the format 03XXXXXXXXX.",
            'father_occupation.regex' => "Father's occupation may only contain letters, numbers, spaces, dots, dashes, and slashes.",
            'postal_address.regex' => 'Postal address may only contain letters, numbers, spaces, dots, commas, dashes, and slashes.',
            'permanent_address.regex' => 'Permanent address may only contain letters, numbers, spaces, dots, commas, dashes, and slashes.',
            'education.*.degree.regex' => 'Degree may only contain letters, numbers, spaces, dots, dashes, and slashes.',
            'education.*.major_subject.regex' => 'Major subject may only contain letters, numbers, spaces, commas, dots, dashes, and slashes.',
        ]);

        // Business rule: Matric/O-Level and Intermediate/A-Level are mandatory
        $validator->after(function ($validator) use ($request) {
            $educationRows = $request->input('education', []);
            $hasMatric = false;
            $hasIntermediate = false;

            foreach ($educationRows as $row) {
                $degree = strtolower($row['degree'] ?? '');
                if (str_contains($degree, 'matric') || str_contains($degree, 'o level') || str_contains($degree, 'o-level')) {
                    $hasMatric = true;
                }
                if (str_contains($degree, 'intermediate') || str_contains($degree, 'f.sc') || str_contains($degree, 'fsc')
                    || str_contains($degree, 'a level') || str_contains($degree, 'a-level') || str_contains($degree, 'hssc')) {
                    $hasIntermediate = true;
                }
            }

            if (!$hasMatric) {
                $validator->errors()->add('education', 'Matric / O-Level qualification is mandatory. Please add it before saving.');
            }
            if (!$hasIntermediate) {
                $validator->errors()->add('education', 'Intermediate / A-Level qualification is mandatory. Please add it before saving.');
            }
        });

        $validated = $validator->validate();

        $candidateLoginId = Auth::guard('web')->id();

        // Create or update candidate profile
        $candidate = Candidate::updateOrCreate(
            ['candidate_login_id' => $candidateLoginId],
            [
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'cnic' => $request->cnic,
                'dob' => $request->dob,
                'postal_address' => $request->postal_address,
                'permanent_address' => $request->permanent_address,
                'district' => $request->district,
                'gender' => $request->gender,
                'mobile_no' => $request->mobile_no,
                'alternative_mobile_no' => $request->alternative_mobile_no,
                'religion' => $request->religion,
                'marital_status' => $request->marital_status,
                'province_of_domicile' => $request->province_of_domicile,
                'district_of_domicile' => $request->district_of_domicile,
                'father_name' => $request->father_name,
                'father_cnic' => $request->father_cnic,
                'father_occupation' => $request->father_occupation,
                'father_mobile_no' => $request->father_mobile_no,
                'father_email' => $request->father_email,
            ]
        );

        // Replace education records
        Education::where('candidate_id', $candidate->id)->delete();

        foreach ($request->education as $edu) {
            Education::create([
                'candidate_id' => $candidate->id,
                'degree' => $edu['degree'],
                'selected_subject' => $edu['major_subject'] ?? null,
                'date_of_passing' => $edu['date_of_passing'],
                'markstype_id' => $edu['marks_type_id'],
                'obtained_marks' => $edu['obtained_marks'],
                'total_marks' => $edu['total_marks'],
                'boarduniversity_id' => $edu['board_university_id'],
            ]);
        }

        return redirect()->route('document.edit')->with('success', 'Profile saved successfully! Now add your documents.');
    }

    // Step 4: View Profile (read-only)
    public function view()
    {
        $candidateLoginId = Auth::guard('web')->id();
        $candidate = Candidate::with('education.marksType', 'education.boardUniversity')
            ->where('candidate_login_id', $candidateLoginId)
            ->first();

        return view('candidate.profile-view', compact('candidate'));
    }
}