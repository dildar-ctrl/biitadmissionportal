@extends('admin.layout')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* ---------- Process Stepper ---------- */
    .stepper-title {
        text-align: center;
        color: var(--primary);
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .process-stepper {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 32px;
    }

    .step-card {
        background-color: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 190px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .step-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    .step-card.active {
        border-color: var(--primary);
        box-shadow: 0 4px 14px rgba(12, 115, 71, 0.15);
    }

    .step-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0c7347, #06b6d4);
        color: #fff;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.95rem;
    }

    .step-text strong {
        display: block;
        font-size: 0.88rem;
        color: var(--text-dark);
    }

    .step-text span {
        font-size: 0.72rem;
        color: var(--text-muted);
    }

    .step-arrow {
        color: #c7c9dc;
        font-size: 0.9rem;
    }

    @media (max-width: 900px) {
        .step-arrow { display: none; }
        .process-stepper { flex-direction: column; align-items: stretch; }
        .step-card { min-width: 0; }
    }

    /* ---------- Instructions Panel ---------- */
    .instructions-panel {
        background-color: #fff;
        border-radius: 14px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .instructions-header {
        background-color: var(--primary);
        color: #fff;
        text-align: center;
        font-weight: 700;
        letter-spacing: 0.04em;
        padding: 16px;
        font-size: 0.95rem;
    }

    .instructions-body {
        padding: 22px;
    }

    .instruction-item {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 18px 20px;
        margin-bottom: 14px;
    }

    .instruction-item:last-child {
        margin-bottom: 0;
    }

    .instruction-item h6 {
        color: var(--primary);
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 8px;
    }

    .instruction-item p {
        font-size: 0.85rem;
        color: #374151;
        margin-bottom: 4px;
    }

    .instruction-item .urdu {
        direction: rtl;
        text-align: right;
        font-size: 0.95rem;
        color: #374151;
        line-height: 1.9;
    }
</style>
@endpush

@section('content')

<div class="stepper-title">Application Process</div>

<div class="process-stepper">
    <a href="{{ route('dashboard') }}" class="step-card {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <div class="step-circle">1</div>
        <div class="step-text"><strong>Dashboard</strong><span>Instructions</span></div>
    </a>
    <i class="fas fa-arrow-right step-arrow"></i>

    <a href="{{ route('profile.edit') }}" class="step-card {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <div class="step-circle">2</div>
        <div class="step-text"><strong>Profile</strong><span>Create / Update</span></div>
    </a>
    <i class="fas fa-arrow-right step-arrow"></i>

    <a href="{{ route('document.edit') }}" class="step-card {{ request()->routeIs('document.*') ? 'active' : '' }}">
        <div class="step-circle">3</div>
        <div class="step-text"><strong>Documents</strong><span>Upload Required Files</span></div>
    </a>
    <i class="fas fa-arrow-right step-arrow"></i>

    <a href="{{ route('profile.view') }}" class="step-card {{ request()->routeIs('profile.view') ? 'active' : '' }}">
        <div class="step-circle">4</div>
        <div class="step-text"><strong>View Profile</strong><span>Review Your Details</span></div>
    </a>
    <i class="fas fa-arrow-right step-arrow"></i>

    <a href="{{ route('apply.index') }}" class="step-card {{ request()->routeIs('apply.*') ? 'active' : '' }}">
        <div class="step-circle">5</div>
        <div class="step-text"><strong>Apply & Submit</strong><span>Select Program, Download/Print</span></div>
    </a>
</div>

<div class="instructions-panel">
    <div class="instructions-header">IMPORTANT INSTRUCTIONS</div>

    <div class="instructions-body">
        <div class="instruction-item">
            <h6>1. Editing Your Application</h6>
            <p>You can update your profile and application details anytime before the closing date to correct any mistakes or missing information.</p>
            <p class="urdu">امیدوار اپنی درخواست میں کسی بھی غلطی یا کمی کو آخری تاریخ تک درست کر سکتے ہیں۔</p>
        </div>

        <div class="instruction-item">
            <h6>2. Document Requirements</h6>
            <p>All uploaded documents (CNIC, certificates, photograph) must be clear and valid. Applications with incomplete or unclear documents may be rejected.</p>
            <p class="urdu">تمام اپلوڈ کردہ دستاویزات واضح اور درست ہونی چاہئیں۔ نامکمل یا غیر واضح دستاویزات والی درخواستیں مسترد کی جا سکتی ہیں۔</p>
        </div>

        <div class="instruction-item">
            <h6>3. Final Data</h6>
            <p>The information provided in your online application will be considered final once the test process begins.</p>
            <p class="urdu">آن لائن درخواست میں فراہم کردہ معلومات کو حتمی تصور کیا جائے گا۔</p>
        </div>

        <div class="instruction-item">
            <h6>4. Test Details</h6>
            <p>Your test date, time, and venue will be mentioned on your Roll Number Slip inside this portal. No separate call letter will be issued.</p>
            <p class="urdu">ٹیسٹ کی تاریخ، وقت اور مقام کا ذکر رول نمبر سلپ پر کیا جائے گا۔ امیدواروں کو کوئی علیحدہ کال لیٹر جاری نہیں کیا جائے گا۔</p>
        </div>
    </div>
</div>

@endsection