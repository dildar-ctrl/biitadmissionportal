{{-- =========================================================
     Apply Now Modal
     Include this on the public website page with: @include('partials.apply-now-modal')
     Trigger it with any button: <button data-bs-toggle="modal" data-bs-target="#applyNowModal">Apply Now</button>
========================================================= --}}

<div class="modal fade" id="applyNowModal" tabindex="-1" aria-labelledby="applyNowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background-color: #3e4095; color: white; border: none;">
                <h5 class="modal-title" id="applyNowModalLabel">Apply for Admission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding: 40px 30px;">
                <p style="font-size: 1.1rem; color: #333; margin-bottom: 30px;">
                    To apply for admission, you need a candidate account. Do you already have one?
                </p>

                <a href="{{ route('login') }}" class="btn w-100 mb-3"
                   style="background-color: #3e4095; color: white; height: 50px; font-weight: 700; border-radius: 6px;">
                    <i class="fas fa-sign-in-alt me-2"></i>Already Have an Account
                </a>

                <a href="{{ route('signup') }}" class="btn w-100"
                   style="background-color: #fff; color: #3e4095; border: 2px solid #3e4095; height: 50px; font-weight: 700; border-radius: 6px;">
                    <i class="fas fa-user-plus me-2"></i>Create New Account
                </a>
            </div>
        </div>
    </div>
</div>