<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Candidate;
use App\Models\Education;

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

        return view('candidate.profile', compact(
            'candidate',
            'candidateLogin',
            'educations'
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
            'gender' => ['required', 'in:Male,Female,Other'],
            'mobile_no' => ['required', 'regex:/^03\d{9}$/'],
            'religion' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'marital_status' => ['required', 'in:Single,Married'],
            'nationality' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'medicalissue' => ['required', 'string', 'max:100'],
            'activities' => ['nullable', 'string', 'max:100'],
            'PMASREGNO' => ['nullable', 'string', 'max:100'],

            // ---- Father / Guardian details ----
            'father_name' => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'father_cnic' => ['required', 'regex:/^\d{5}-\d{7}-\d{1}$/', 'different:cnic'],
            'relationshipwithguardian' => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z\s]+$/'],
            'father_mobile_no' => ['required', 'regex:/^03\d{9}$/'],

            // ---- Emergency contact ----
            'emergencycontactname' => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'emergencycontactrelationship' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'emergencycontact' => ['required', 'regex:/^03\d{9}$/', 'different:mobile_no'],

            // ---- Education (repeatable array) ----
            'education' => ['required', 'array', 'min:1'],
            'education.*.degree' => ['required', 'string', 'max:150', 'regex:/^[a-zA-Z0-9\s\.\-\/]+$/'],
            'education.*.boardname' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\.\-\/]+$/'],
            'education.*.majorsubjects' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\,\.\-\/]+$/'],
            'education.*.date_of_passing' => ['required', 'date', 'before_or_equal:today'],
            'education.*.registrationno' => ['nullable', 'string', 'max:50'],
            'education.*.obtained_marks' => ['required', 'numeric', 'min:0'],
            'education.*.total_marks' => ['required', 'numeric', 'min:1'],
            'education.*.division' => ['nullable', 'string', 'max:50'],
        ], [
            'fname.regex' => 'First name may only contain letters.',
            'lname.regex' => 'Last name may only contain letters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'cnic.regex' => 'CNIC must be in the format 12345-1234567-1.',
            'mobile_no.regex' => 'Mobile number must be in the format 03XXXXXXXXX.',
            'religion.regex' => 'Religion may only contain letters.',
            'nationality.regex' => 'Nationality may only contain letters.',
            'father_name.regex' => "Father's name may only contain letters.",
            'father_cnic.regex' => "Father's CNIC must be in the format 12345-1234567-1.",
            'father_cnic.different' => "Father's CNIC cannot be the same as your own CNIC.",
            'father_mobile_no.regex' => "Father's mobile number must be in the format 03XXXXXXXXX.",
            'relationshipwithguardian.regex' => 'Relationship with guardian may only contain letters.',
            'postal_address.regex' => 'Postal address may only contain letters, numbers, spaces, dots, commas, dashes, and slashes.',
            'permanent_address.regex' => 'Permanent address may only contain letters, numbers, spaces, dots, commas, dashes, and slashes.',
            'emergencycontactname.regex' => 'Emergency contact name may only contain letters.',
            'emergencycontactrelationship.regex' => 'Emergency contact relationship may only contain letters.',
            'emergencycontact.regex' => 'Emergency contact number must be in the format 03XXXXXXXXX.',
            'emergencycontact.different' => 'Emergency contact number must be different from your own mobile number.',
            'education.*.degree.regex' => 'Degree may only contain letters, numbers, spaces, dots, dashes, and slashes.',
            'education.*.boardname.regex' => 'Board/University name may only contain letters, numbers, spaces, dots, dashes, and slashes.',
            'education.*.majorsubjects.regex' => 'Major subjects may only contain letters, numbers, spaces, commas, dots, dashes, and slashes.',
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
                'gender' => $request->gender,
                'mobile_no' => $request->mobile_no,
                'religion' => $request->religion,
                'marital_status' => $request->marital_status,
                'nationality' => $request->nationality,
                'medicalissue' => $request->medicalissue,
                'activities' => $request->activities,
                'PMASREGNO' => $request->PMASREGNO,
                'father_name' => $request->father_name,
                'father_cnic' => $request->father_cnic,
                'relationshipwithguardian' => $request->relationshipwithguardian,
                'father_mobile_no' => $request->father_mobile_no,
                'emergencycontactname' => $request->emergencycontactname,
                'emergencycontactrelationship' => $request->emergencycontactrelationship,
                'emergencycontact' => $request->emergencycontact,
            ]
        );

        // Replace education records
        Education::where('candidate_id', $candidate->id)->delete();

        foreach ($request->education as $edu) {
            $obtained = (float) $edu['obtained_marks'];
            $total = (float) $edu['total_marks'];

            Education::create([
                'candidate_id' => $candidate->id,
                'degree' => $edu['degree'],
                'boardname' => $edu['boardname'],
                'majorsubjects' => $edu['majorsubjects'] ?? null,
                'date_of_passing' => $edu['date_of_passing'],
                'registrationno' => $edu['registrationno'] ?? null,
                'obtained_marks' => $obtained,
                'total_marks' => $total,
                'percentage' => $total > 0 ? round(($obtained / $total) * 100, 2) : 0,
                'division' => $edu['division'] ?? null,
            ]);
        }

        return redirect()->route('document.edit')->with('success', 'Profile saved successfully! Now add your documents.');
    }

    // Step 4: View Profile (read-only)
    public function view()
    {
        return redirect()->route('profile.review');
    }
}