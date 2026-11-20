@extends('layouts.app')

@section('title', 'Undertakings')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">

<style>
    .undertakings-container {
        max-width: 900px;
    }

    .progress-indicator {
        display: flex;
        gap: 12px;
        margin-bottom: 32px;
        justify-content: center;
    }

    .progress-step {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .progress-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        background: #e5e7eb;
        color: #6b7280;
        transition: all 0.3s ease;
    }

    .progress-step.active .progress-circle {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: #fff;
}

    .progress-step.completed .progress-circle {
        background: #22c55e;
        color: #fff;
    }

    .progress-step.completed .progress-circle::after {
        content: '✓';
    }

    .progress-step.completed .progress-circle {
        font-size: 0;
    }

    .progress-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        text-align: center;
        min-width: 60px;
    }

    .progress-step.completed .progress-label {
        color: #22c55e;
    }

    .progress-step.active .progress-label {
        color: var(--primary);
        font-weight: 700;
    }

    .undertaking-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--border);
        padding: 32px;
        margin-bottom: 24px;
        display: none;
    }

    .undertaking-card.active {
        display: block;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .undertaking-card h4 {
        font-weight: 800;
        font-size: 1.3rem;
        margin-bottom: 16px;
        color: var(--text-dark);
    }

    .undertaking-card .undertaking-content {
        background: #f8f9fa;
        border-left: 4px solid var(--primary);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        line-height: 1.8;
        font-size: 0.95rem;
        color: var(--text-dark);
        max-height: 400px;
        overflow-y: auto;
    }

    .undertaking-content p {
        margin-bottom: 12px;
    }

    .undertaking-content strong {
        display: block;
        margin-top: 16px;
        margin-bottom: 8px;
        color: var(--text-dark);
    }

    .undertaking-content ul {
        list-style: none;
        padding-left: 0;
        margin: 12px 0;
    }

    .undertaking-content li {
        padding-left: 24px;
        position: relative;
        margin-bottom: 8px;
    }

    .undertaking-content li::before {
        content: '•';
        position: absolute;
        left: 8px;
        color: var(--primary);
        font-weight: 700;
    }

    .acceptance-checkbox {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 24px 0;
        padding: 16px;
        background: rgba(34, 197, 94, 0.08);
        border-radius: 8px;
        border: 1px solid #d1fae5;
    }

    .acceptance-checkbox input[type="checkbox"] {
        width: 20px;
        height: 20px;
        accent-color: var(--primary);
        cursor: pointer;
        flex-shrink: 0;
    }

    .acceptance-checkbox label {
        margin: 0;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--text-dark);
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        justify-content: flex-end;
    }

   .btn-primary {
    background: linear-gradient(135deg, #279a4f 0%, #17753b 100%);
    color: #fff;
    border: none;
    padding: 12px 28px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: background 0.2s ease;
}

.btn-primary:hover:not(:disabled) {
    background: var(--primary-dark);
    opacity: 1;
}

    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: var(--primary);
        color: #fff;
    }

    .completion-message {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid #d1fae5;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        display: none;
    }

    .completion-message.show {
        display: block;
        animation: slideIn 0.3s ease;
    }

    .completion-message h4 {
        color: #22c55e;
        font-weight: 800;
        margin-bottom: 12px;
    }

    .completion-message p {
        color: var(--text-dark);
        margin-bottom: 16px;
    }

    .completion-message .btn-primary {
        margin-top: 16px;
    }

    .loading {
        display: none;
        text-align: center;
        padding: 20px;
    }

    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #e5e7eb;
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')

<div class="undertakings-container">
    <div class="stepper-title">Undertakings & Terms</div>

    <!-- PROGRESS INDICATOR -->
    <div class="progress-indicator">
        <div class="progress-step {{ !$undertaking1_accepted ? 'active' : 'completed' }}" id="step1">
            <div class="progress-circle">1</div>
            <div class="progress-label">Admission</div>
        </div>

        <div class="progress-step {{ $undertaking1_accepted && !$undertaking2_accepted ? 'active' : ($undertaking2_accepted ? 'completed' : '') }}" id="step2">
            <div class="progress-circle">2</div>
            <div class="progress-label">Refund Policy</div>
        </div>

        <div class="progress-step {{ $undertaking1_accepted && $undertaking2_accepted && !$undertaking3_accepted ? 'active' : ($undertaking3_accepted ? 'completed' : '') }}" id="step3">
            <div class="progress-circle">3</div>
            <div class="progress-label">Conduct</div>
        </div>
    </div>

    <!-- UNDERTAKING 1: ADMISSION ON PROVISIONAL BASIS -->
    <div class="undertaking-card {{ !$undertaking1_accepted ? 'active' : '' }}" id="undertaking1">
        <h4>Undertaking on Provisional Admission Basis</h4>

        <div class="undertaking-content">
        <p>
    I, <span style="font-weight: 700;">{{ strtoupper(($candidate->fname ?? '') . ' ' . ($candidate->lname ?? '')) }}</span>, hereby certify that the information provided in my application for admission to 
    Barani Institute of Information Technology (BIIT) affiliated with PMAS - Arid Agriculture University, 
    is true and accurate to the best of my knowledge.
</p>

            <strong>Key Acknowledgments:</strong>
            <ul>
                <li>I understand that my admission is provisional and subject to verification of documents</li>
                <li>I will abide by all academic and conduct rules of the institution</li>
                <li>I declare that all submitted documents are original or authentic copies</li>
                <li>I am aware that providing false information may lead to rejection or expulsion</li>
                <li>I accept any decision regarding admission that BIIT may make after document verification</li>
            </ul>

            <p>
                I hereby undertake that I shall not pursue any claims if my provisional admission is cancelled after 
                document verification, and that BIIT has the right to cancel admission at any stage if found ineligible.
            </p>
        </div>

        <div class="acceptance-checkbox">
            <input type="checkbox" id="checkbox1" onchange="toggleAcceptButton(1)">
            <label for="checkbox1">I accept the undertaking on provisional admission basis</label>
        </div>

        <div class="loading" id="loading1">
            <div class="spinner"></div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-primary" id="acceptBtn1" disabled onclick="acceptUndertaking(1)">
                Accept & Continue <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- UNDERTAKING 2: REFUND POLICY -->
    <div class="undertaking-card {{ $undertaking1_accepted && !$undertaking2_accepted ? 'active' : '' }}" id="undertaking2">
        <h4>Refund Policy Acknowledgment</h4>

        <div class="undertaking-content">
    @php
        // Group programs that share the exact same tuition + admission fee,
        // so identical fee schedules aren't repeated table-for-table.
        $feeGroups = collect($programs)->groupBy(function ($p) {
            return ($p['tuition_fee'] ?? 'na') . '-' . ($p['admission_fee'] ?? 'na');
        });
    @endphp

    @foreach($feeGroups as $group)
        @php $first = $group->first(); @endphp
        <div style="border: 1px solid var(--border); border-radius: 12px; padding: 0; margin-bottom: 20px; overflow: hidden; background: #fff;">

            {{-- Header bar --}}
            <div style="background: var(--primary); color: #fff; padding: 14px 20px;">
                <div style="font-weight: 800; font-size: 1rem; margin-bottom: 4px;">Fee Schedule &amp; Refund Undertaking</div>
                <div style="font-size: 0.85rem; opacity: 0.95;">
                    {{ $group->pluck('name')->implode(' / ') }}
                </div>
            </div>

            <div style="padding: 18px 20px;">
                @if($first['tuition_fee'] !== null)
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 0.9rem;">
                        <tr>
                            <td style="border: 1px solid var(--border); padding: 8px; font-weight: 700; background: var(--bg-soft); width: 45%;">Total Fee</td>
                            <td style="border: 1px solid var(--border); padding: 8px;">Rs. {{ number_format($first['total_fee']) }}/-</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid var(--border); padding: 8px; font-weight: 700; background: var(--bg-soft);">Tuition Fee (per semester)</td>
                            <td style="border: 1px solid var(--border); padding: 8px;">Rs. {{ number_format($first['tuition_fee']) }}/-</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid var(--border); padding: 8px; font-weight: 700; background: var(--bg-soft);">Admission Fee (One time, Non-Refundable)</td>
                            <td style="border: 1px solid var(--border); padding: 8px;">Rs. {{ number_format($first['admission_fee']) }}/-</td>
                        </tr>
                    </table>

                    <p style="margin-bottom: 12px; font-size: 0.9rem;">
                        <strong>NOTE:</strong> Admission Fee (Rs. {{ number_format($first['admission_fee']) }}/-) is
                        <span style="text-decoration: underline; font-weight: 700; color: var(--primary);">non-refundable</span>. However,
                        Tuition Fee (Rs. {{ number_format($first['tuition_fee']) }}/-) is refundable as per the given schedule:
                    </p>

                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        <tr style="background: var(--primary); color: #fff;">
                            <th style="border: 1px solid var(--border); padding: 8px;">%age of Fee</th>
                            <th style="border: 1px solid var(--border); padding: 8px;">Timeline for Semester</th>
                            <th style="border: 1px solid var(--border); padding: 8px;">Tentative Date (From)</th>
                            <th style="border: 1px solid var(--border); padding: 8px;">Tentative Date (To)</th>
                        </tr>
                        @foreach($first['refund_breakdown'] as $i => $slab)
                            <tr style="{{ $i % 2 == 1 ? 'background: var(--bg-light);' : 'background: #fff;' }}">
                                <td style="border: 1px solid var(--border); padding: 8px; text-align: center;">
                                    @if($slab['percent'] > 0)
                                        ({{ $slab['percent'] }}%) Rs. {{ number_format($slab['amount']) }}/- Fee Refund
                                    @else
                                        (0%) No Refund
                                    @endif
                                </td>
                                <td style="border: 1px solid var(--border); padding: 8px; text-align: center;">
                                    @if($slab['to_day'] !== null)
                                        Up to {{ $slab['to_day'] }}{{ $slab['to_day'] == 1 ? 'st' : ($slab['to_day'] == 2 ? 'nd' : ($slab['to_day'] == 3 ? 'rd' : 'th')) }} day of Commencement of Semester
                                    @else
                                        From {{ $slab['from_day'] }}st day of Commencement of Semester
                                    @endif
                                </td>
                                <td style="border: 1px solid var(--border); padding: 8px; text-align: center;">
                                    {{ $slab['date_from'] ? $slab['date_from']->format('jS M, Y') : 'TBD' }}
                                </td>
                                <td style="border: 1px solid var(--border); padding: 8px; text-align: center;">
                                    {{ $slab['date_to'] ? $slab['date_to']->format('jS M, Y') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </table>

                    <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 10px;">
                        NOTE: Fee will not be refunded in any case other than the policy mentioned above — as per HEC National Fee Refund Policy.
                    </p>
                @else
                    <p style="font-size: 0.85rem; color: #d33;">Fee details not available for this program yet.</p>
                @endif
            </div>
        </div>
    @endforeach

    <p>
        I acknowledge that I have reviewed and understood the refund policy of BIIT for each of my selected program(s)
        above and accept all terms and conditions outlined therein.
    </p>
</div>

        <div class="acceptance-checkbox">
            <input type="checkbox" id="checkbox2" onchange="toggleAcceptButton(2)">
            <label for="checkbox2">I accept the refund policy terms and conditions</label>
        </div>

        <div class="loading" id="loading2">
            <div class="spinner"></div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-secondary" onclick="goBack(2)">
                <i class="fas fa-chevron-left"></i> Previous
            </button>
            <button type="button" class="btn-primary" id="acceptBtn2" disabled onclick="acceptUndertaking(2)">
                Accept & Continue <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- UNDERTAKING 3: CONDUCT & COMPLIANCE -->
    <div class="undertaking-card {{ $undertaking1_accepted && $undertaking2_accepted && !$undertaking3_accepted ? 'active' : '' }}" id="undertaking3">
        <h4>Undertaking to Abide by Rules and Regulations</h4>

        <div class="undertaking-content">
            <p>
                I hereby declare that I will abide by all rules, regulations, and codes of conduct established 
                by Barani Institute of Information Technology and PMAS - Arid Agriculture University.
            </p>

            <strong>Dress Code:</strong>
            <ul>
                <li>Students should wear formal attire (suits/dress) to campus and official events</li>
                <li>No casual or beachwear allowed in academic areas</li>
                <li>Shorts and sleeveless shirts are not permitted</li>
                <li>Footwear must be worn at all times on campus</li>
            </ul>

            <strong>Conduct Standards:</strong>
            <ul>
                <li>No smoking, vaping, or use of tobacco products on campus</li>
                <li>Strictly no drugs, alcohol, or any prohibited substances</li>
                <li>No harassment, bullying, or inappropriate behavior towards other students/staff</li>
                <li>Maintain respectful communication on and off campus</li>
                <li>No use of mobile devices during classes without instructor permission</li>
            </ul>

            <strong>Academic Integrity:</strong>
            <ul>
                <li>All assignments, projects, and exams must be completed honestly</li>
                <li>Plagiarism and cheating are strictly prohibited and may result in expulsion</li>
                <li>Proper citation of sources is mandatory for all academic work</li>
            </ul>

            <strong>Disciplinary Action:</strong>
            <ul>
                <li>Violation of these rules may result in warnings, fines, suspension, or expulsion</li>
                <li>The institution reserves the right to take appropriate action based on severity</li>
                <li>Decision of the disciplinary committee is final and binding</li>
            </ul>

            <p>
                I acknowledge that I have read and understood all rules and regulations, and I commit to 
                following them throughout my academic tenure at BIIT.
            </p>
        </div>

        <div class="acceptance-checkbox">
            <input type="checkbox" id="checkbox3" onchange="toggleAcceptButton(3)">
            <label for="checkbox3">I accept all rules, regulations, and codes of conduct</label>
        </div>

        <div class="loading" id="loading3">
            <div class="spinner"></div>
        </div>

        <div class="button-group">
            <button type="button" class="btn-secondary" onclick="goBack(3)">
                <i class="fas fa-chevron-left"></i> Previous
            </button>
            <button type="button" class="btn-primary" id="acceptBtn3" disabled onclick="acceptUndertaking(3)">
                Accept & Submit Application <i class="fas fa-check"></i>
            </button>
        </div>
    </div>

    <!-- COMPLETION MESSAGE -->
    <div class="completion-message" id="completionMessage">
        <h4><i class="fas fa-check-circle"></i> Application Successfully Submitted!</h4>
        <p>All undertakings have been accepted. Your application is now submitted and you can download or print it.</p>
        <a href="{{ route('apply.status') }}" class="btn-primary" style="display: inline-block; text-decoration: none;">
            View Your Application <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const APPLICATION_NO = '{{ $application_no }}';
    const UNDERTAKING1_ACCEPTED = {{ $undertaking1_accepted ? 'true' : 'false' }};
    const UNDERTAKING2_ACCEPTED = {{ $undertaking2_accepted ? 'true' : 'false' }};
    const UNDERTAKING3_ACCEPTED = {{ $undertaking3_accepted ? 'true' : 'false' }};

    // Check if all completed on page load
    if (UNDERTAKING1_ACCEPTED && UNDERTAKING2_ACCEPTED && UNDERTAKING3_ACCEPTED) {
        showCompletionMessage();
    }

    function toggleAcceptButton(undertakingNum) {
        const checkbox = document.getElementById(`checkbox${undertakingNum}`);
        const button = document.getElementById(`acceptBtn${undertakingNum}`);
        button.disabled = !checkbox.checked;
    }

    function acceptUndertaking(undertakingNum) {
        const checkbox = document.getElementById(`checkbox${undertakingNum}`);
        
        if (!checkbox.checked) {
            alert('Please accept the undertaking first.');
            return;
        }

        const loading = document.getElementById(`loading${undertakingNum}`);
        const button = document.getElementById(`acceptBtn${undertakingNum}`);

        loading.style.display = 'block';
        button.disabled = true;

        fetch('{{ route('undertakings.accept') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                application_no: APPLICATION_NO,
                undertaking_type: undertakingNum,
            }),
        })
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            button.disabled = false;

            if (data.success) {
                // Hide current form
                document.getElementById(`undertaking${undertakingNum}`).classList.remove('active');
                
                // Update progress
                document.getElementById(`step${undertakingNum}`).classList.remove('active');
                document.getElementById(`step${undertakingNum}`).classList.add('completed');

                if (undertakingNum < 3) {
                    // Show next form
                    const nextNum = undertakingNum + 1;
                    document.getElementById(`undertaking${nextNum}`).classList.add('active');
                    document.getElementById(`step${nextNum}`).classList.add('active');
                } else {
                    // All completed - submit application
                    submitApplication();
                }
            } else {
                alert(data.message || 'Failed to accept undertaking.');
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            button.disabled = false;
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    function goBack(undertakingNum) {
        const currentNum = undertakingNum;
        const prevNum = currentNum - 1;

        document.getElementById(`undertaking${currentNum}`).classList.remove('active');
        document.getElementById(`undertaking${prevNum}`).classList.add('active');
        document.getElementById(`step${currentNum}`).classList.remove('active');
        document.getElementById(`step${prevNum}`).classList.add('active');
    }

    function submitApplication() {
        const submitBtn = document.createElement('form');
        submitBtn.method = 'POST';
        submitBtn.action = '{{ route('undertakings.submit') }}';
        submitBtn.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="application_no" value="{{ $application_no }}">
        `;

        fetch(submitBtn.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                application_no: APPLICATION_NO,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.message) {
                showCompletionMessage();
            } else {
                alert('Failed to submit application.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCompletionMessage(); // Show anyway for UX
        });
    }

    function showCompletionMessage() {
        document.getElementById('undertaking3').classList.remove('active');
        document.getElementById('completionMessage').classList.add('show');
    }
</script>
@endpush