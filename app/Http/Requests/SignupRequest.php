<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise the input before the rules run, so the server accepts
     * the same formats the browser script produces.
     */
    protected function prepareForValidation(): void
    {
        $cnic = preg_replace('/\D/', '', (string) $this->input('cnic'));
        if (strlen($cnic) === 13) {
            $cnic = substr($cnic, 0, 5) . '-' . substr($cnic, 5, 7) . '-' . substr($cnic, 12, 1);
        } else {
            $cnic = (string) $this->input('cnic');
        }

        $mobile = preg_replace('/\D/', '', (string) $this->input('mobile_no'));
        if (str_starts_with($mobile, '92') && strlen($mobile) === 12) {
            $mobile = '0' . substr($mobile, 2);
        }

        $this->merge([
            'fname'     => trim((string) $this->input('fname')),
            'lname'     => trim((string) $this->input('lname')),
            'cnic'      => $cnic,
            'mobile_no' => $mobile,
            'email'     => strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'fname'     => ['required', 'string', 'min:2', 'max:50', "regex:/^[A-Za-z][A-Za-z .'-]*$/"],
            'lname'     => ['required', 'string', 'min:2', 'max:50', "regex:/^[A-Za-z][A-Za-z .'-]*$/"],
            'cnic'      => ['required', 'regex:/^\d{5}-\d{7}-\d$/', 'unique:candidatelogin,cnic'],
            'mobile_no' => ['required', 'regex:/^03\d{9}$/'],
            'email'     => ['required', 'email:rfc', 'max:100', 'unique:candidatelogin,email'],
            'password'  => ['required', 'string', 'max:64', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public function attributes(): array
    {
        return [
            'fname'     => 'first name',
            'lname'     => 'last name',
            'cnic'      => 'CNIC',
            'mobile_no' => 'mobile number',
        ];
    }

    public function messages(): array
    {
        return [
            'fname.required'     => 'First name is required.',
            'fname.regex'        => 'First name can only contain letters, spaces, dots, apostrophes and hyphens.',
            'lname.required'     => 'Last name is required.',
            'lname.regex'        => 'Last name can only contain letters, spaces, dots, apostrophes and hyphens.',
            'cnic.required'      => 'CNIC is required.',
            'cnic.regex'         => 'Enter a valid 13-digit CNIC, e.g. 12345-1234567-1.',
            'cnic.unique'        => 'An account with this CNIC already exists. Please log in instead.',
            'mobile_no.required' => 'Mobile number is required.',
            'mobile_no.regex'    => 'Enter a valid mobile number, e.g. 03001234567.',
            'email.required'     => 'Email is required.',
            'email.email'        => 'Enter a valid email address, e.g. name@example.com.',
            'email.unique'       => 'An account with this email already exists. Please log in instead.',
            'password.required'  => 'Password is required.',
            'password.confirmed' => 'Passwords do not match.',
        ];
    }
}