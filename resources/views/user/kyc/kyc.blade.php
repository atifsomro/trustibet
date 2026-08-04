<div class="kyc">
    <div class="grid xl:grid-cols-12 gap-6">
        {{-- Left --}}
        <div class="xl:col-span-8">
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-2">
                <div class="mb-8">
                    @if (!$kyc || $kyc->status == 'rejected')
                        <h3>
                            KYC Verification
                        </h3>

                        <p class="mt-2 opacity-70">
                            Complete your identity verification to unlock withdrawals and premium features.
                        </p>
                    @elseif($kyc->status == 'pending')
                        <h3>
                            KYC Verification Submitted
                        </h3>

                        <p class="mt-2 opacity-70">
                            Your documents have been submitted and are currently under review.
                        </p>
                    @elseif($kyc->status == 'approved')
                        <h3>
                            KYC Verified
                        </h3>

                        <p class="mt-2 opacity-70">
                            Your identity verification has been successfully approved.
                        </p>
                    @endif
                </div>
                @if (!$kyc || $kyc->status == 'rejected')
                    <form action="{{ route('wallet.kyc.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- Personal Information --}}
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="mb-2 block">
                                    Full Name
                                </label>
                                <input type="text" name="full_name"
                                    value="{{ old('full_name', $kyc->full_name ?? '') }}"
                                    placeholder="Enter your full name"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                    required>
                            </div>
                            <div>
                                <label class="mb-2 block">
                                    Date of Birth
                                </label>
                                <input type="date"
                                    value="{{ old('date_of_birth', isset($kyc->date_of_birth) ? $kyc->date_of_birth->format('Y-m-d') : '') }}"
                                    name="date_of_birth"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                    required>
                            </div>
                            <div>
                                <label class="mb-2 block">
                                    Country
                                </label>
                                <input type="text" value="{{ old('country', $kyc->country ?? 'Pakistan') }}"
                                    name="country" value="Pakistan"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 opacity-70"
                                    required>
                            </div>
                            <div>
                                <label class="mb-2 block">
                                    ID Type
                                </label>
                                <select name="id_type"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3"
                                    required>

                                    <option value="CNIC"
                                        {{ old('id_type', $kyc->id_type ?? '') == 'CNIC' ? 'selected' : '' }}>
                                        CNIC
                                    </option>

                                    <option value="Passport"
                                        {{ old('id_type', $kyc->id_type ?? '') == 'Passport' ? 'selected' : '' }}>
                                        Passport
                                    </option>

                                    <option value="Driving License"
                                        {{ old('id_type', $kyc->id_type ?? '') == 'Driving License' ? 'selected' : '' }}>
                                        Driving License
                                    </option>

                                </select>
                            </div>
                        </div>
                        {{-- Uploads --}}
                        <div class="mt-4 sm:mt-10">
                            <h4>Upload Documents</h4>
                            <div class="grid lg:grid-cols-3 gap-6 mt-6">
                                {{-- Front --}}
                                <label
                                    class="cursor-pointer rounded-2xl border-2 border-dashed border-brand-border p-8 text-center hover:border-brand-primary transition-colors">

                                    <i class="fa-solid fa-id-card text-xl sm:text-5xl text-brand-primary"></i>

                                    <h5 class="mt-5">Front Side</h5>

                                    @if (isset($kyc) && $kyc->front_image)
                                        <img src="{{ asset('storage/' . $kyc->front_image) }}"
                                            class="w-32 h-20 object-cover rounded-lg mx-auto mt-4">
                                        <p class="text-xs text-green-400 mt-2">
                                            Previous image uploaded
                                        </p>
                                    @else
                                        <p class="mt-2 text-sm opacity-70">
                                            Upload front side of your ID.
                                        </p>
                                    @endif

                                    <p class="upload-status hidden mt-2 text-xs text-green-400 font-medium"></p>

                                    <input type="file" name="front_image" class="hidden"
                                        {{ !$kyc || !$kyc->front_image ? 'required' : '' }}>

                                </label>
                                {{-- Back --}}
                                <label
                                    class="cursor-pointer rounded-2xl border-2 border-dashed border-brand-border p-8 text-center hover:border-brand-primary transition-colors">

                                    <i class="fa-solid fa-address-card text-xl sm:text-5xl text-brand-primary"></i>

                                    <h5 class="mt-5">Back Side</h5>

                                    @if (isset($kyc) && $kyc->back_image)
                                        <img src="{{ asset('storage/' . $kyc->back_image) }}"
                                            class="w-32 h-20 object-cover rounded-lg mx-auto mt-4">

                                        <p class="text-xs text-green-400 mt-2">
                                            Previous image uploaded
                                        </p>
                                    @else
                                        <p class="mt-2 text-sm opacity-70">
                                            Upload back side of your ID.
                                        </p>
                                    @endif

                                    <p class="upload-status hidden mt-2 text-xs text-green-400 font-medium"></p>

                                    <input type="file" name="back_image" class="hidden"
                                        {{ !$kyc || !$kyc->back_image ? 'required' : '' }}>

                                </label>
                                {{-- Selfie --}}
                                <label
                                    class="cursor-pointer rounded-2xl border-2 border-dashed border-brand-border p-8 text-center hover:border-brand-primary transition-colors">

                                    <i class="fa-solid fa-camera text-xl sm:text-5xl text-brand-primary"></i>

                                    <h5 class="mt-5">Selfie</h5>

                                    @if (isset($kyc) && $kyc->selfie_image)
                                        <img src="{{ asset('storage/' . $kyc->selfie_image) }}"
                                            class="w-32 h-20 object-cover rounded-lg mx-auto mt-4">

                                        <p class="text-xs text-green-400 mt-2">
                                            Previous image uploaded
                                        </p>
                                    @else
                                        <p class="mt-2 text-sm opacity-70">
                                            Upload a clear selfie.
                                        </p>
                                    @endif

                                    <p class="upload-status hidden mt-2 text-xs text-green-400 font-medium"></p>

                                    <input type="file" name="selfie_image" class="hidden"
                                        {{ !$kyc || !$kyc->selfie_image ? 'required' : '' }}>

                                </label>
                            </div>
                        </div>
                        {{-- Address --}}
                        <div class="mt-4 sm:mt-10">
                            <h4>Address Information</h4>
                            <div class="grid md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <label class="mb-2 block">
                                        Address
                                    </label>
                                    <input type="text" value="{{ old('address', $kyc->address ?? '') }}"
                                        name="address" placeholder="Street address"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                </div>
                                <div>
                                    <label class="mb-2 block">
                                        City
                                    </label>
                                    <input type="text" name="city" value="{{ old('city', $kyc->city ?? '') }}"
                                        placeholder="Enter city"
                                        class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                        required>
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block">
                                    ID Number
                                </label>
                                <input type="text" name="id_number"
                                    value="{{ old('id_number', $kyc->id_number ?? '') }}"
                                    placeholder="Enter your ID number"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary"
                                    required>
                                <small class="mt-2 block opacity-70">
                                    Enter your CNIC, Passport or Driving License number.
                                </small>
                            </div>
                        </div>
                        {{-- Terms --}}
                        <div class="mt-4 sm:mt-10 space-y-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="confirm_documents" required>
                                <span class="text-[10px] sm:text-sm">
                                    I confirm that all submitted documents belong to me.
                                </span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="agree_policy" required>
                                <span class="text-[10px] sm:text-sm">
                                    I agree to the Claimrise KYC Policy.
                                </span>
                            </label>
                        </div>
                        <button type="submit" class="btn-primary mt-4 sm:mt-10">
                            <i class="fa-solid fa-paper-plane mr-2"></i>
                            Submit Verification
                        </button>
                    </form>
                @else
                    {{-- KYC Submitted Status Card --}}
                    <div class="rounded-3xl border border-brand-border bg-brand-dark p-8 text-center">
                        @if ($kyc->status == 'pending')
                            <i class="fa-solid fa-clock text-5xl text-yellow-500"></i>
                            <h3 class="mt-5">
                                Verification Under Review
                            </h3>
                            <p class="mt-3 opacity-70">
                                Your documents have been submitted successfully.
                                Our team is reviewing your KYC request.
                            </p>
                            <spa class="inline-block mt-5 px-5 py-2 rounded-full bg-yellow-500/20 text-yellow-500">
                                Pending Review
                                </span>
                            @elseif($kyc->status == 'approved')
                                <i class="fa-solid fa-circle-check text-5xl text-green-500"></i>
                                <h3 class="mt-5">
                                    KYC Verified Successfully
                                </h3>
                                <p class="mt-3 opacity-70">
                                    Congratulations! Your identity verification has been approved.
                                    You can now access all premium features.
                                </p>
                                <span class="inline-block mt-5 px-5 py-2 rounded-full bg-green-500/20 text-green-500">
                                    Approved
                                </span>
                        @endif
                        @if ($kyc->verified_at)
                            <p class="mt-5 text-sm opacity-70">
                                Verified on:
                                {{ $kyc->verified_at->format('d M Y') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        {{-- Right --}}
        <div class="xl:col-span-4">
            {{-- Status --}}
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-8">
                <div class="flex flex-col text-center sm:text-start gap-3">
                    <div>
                        <h4>KYC Status</h4>

                        @if (!$kyc)
                            <p class="text-red-500 mt-1">
                                Not Applied
                            </p>
                        @elseif($kyc->status == 'pending')
                            <p class="text-yellow-500 mt-1">
                                Pending Review
                            </p>
                        @elseif($kyc->status == 'approved')
                            <p class="text-green-500 mt-1">
                                Approved
                            </p>
                        @elseif($kyc->status == 'rejected')
                            <p class="text-red-500 mt-1">
                                Rejected
                            </p>
                        @endif

                    </div>

                </div>
                <p class="mt-6 opacity-70">
                    @if (!$kyc)
                        Submit your documents to start verification.
                    @elseif($kyc->status == 'pending')
                        Your documents are being reviewed. Please wait for approval.
                    @elseif($kyc->status == 'approved')
                        Your account has been successfully verified.
                    @elseif($kyc->status == 'rejected')
                        Your KYC was rejected. Please review the reason and submit again.
                    @endif
                </p>
                {{-- Rejection Reason --}}
                @if ($kyc && $kyc->status == 'rejected')
                    <div class="mt-5 rounded-xl bg-red-500/10 p-4 text-red-400">
                        <strong>
                            Reason:
                        </strong>
                        {{ $kyc->rejection_reason }}
                    </div>
                @endif
            </div>
            {{-- Timeline --}}
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-8 mt-3 sm:mt-6">
                <h4>
                    Verification Steps
                </h4>
                <div class="mt-6 space-y-3">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-green-500"></i>
                        <span>
                            Submit Documents
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($kyc && in_array($kyc->status, ['pending', 'approved']))
                            <i class="fa-solid fa-circle-check text-green-500"></i>
                        @else
                            <i class="fa-solid fa-clock text-yellow-500"></i>
                        @endif
                        <span>
                            Under Review
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($kyc && $kyc->status == 'approved')
                            <i class="fa-solid fa-circle-check text-green-500"></i>
                            <span>
                                Approved
                            </span>
                        @else
                            <i class="fa-solid fa-circle text-gray-500"></i>
                            <span>
                                Approved
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Guidelines --}}
            @if(!$kyc || $kyc->status != 'approved')
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-8 mt-3 sm:mt-6">
                <h4>
                    Guidelines
                </h4>
                <ul class="mt-3 sm:mt-6 space-y-3 text-[10px] md:text-sm">
                    <li>✅ Upload clear images.</li>
                    <li>✅ All corners must be visible.</li>
                    <li>✅ No edited or cropped documents.</li>
                    <li>✅ Selfie must match your ID.</li>
                    <li>✅ JPG, PNG & WEBP supported.</li>
                    <li>✅ Maximum file size: 5MB.</li>
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@push('scripts')
    <script>
        (function() {
            // ---- Upload visual feedback (event delegation) ----
            document.addEventListener('change', function(e) {
                try {
                    const input = e.target;
                    if (!input.matches('input[type="file"]')) return;

                    const label = input.closest('label');
                    if (!label) return;

                    const statusEl = label.querySelector('.upload-status');
                    const icon = label.querySelector('i');

                    if (input.files && input.files.length > 0) {
                        label.classList.remove('border-brand-border');
                        label.classList.add('border-green-500', 'bg-green-500/5');
                        icon?.classList.remove('text-brand-primary');
                        icon?.classList.add('text-green-500');
                        if (statusEl) {
                            statusEl.textContent = '✔ ' + input.files[0].name;
                            statusEl.classList.remove('hidden');
                        }
                    } else {
                        label.classList.add('border-brand-border');
                        label.classList.remove('border-green-500', 'bg-green-500/5');
                        icon?.classList.add('text-brand-primary');
                        icon?.classList.remove('text-green-500');
                        statusEl?.classList.add('hidden');
                    }
                } catch (err) {
                    console.error('Upload UI feedback error:', err);
                }
            });

            // ---- ID number validation (event delegation) ----
            document.addEventListener('submit', function(e) {
                try {
                    const kycForm = e.target;
                    if (!kycForm.matches('form[action="{{ route('wallet.kyc.store') }}"]')) return;

                    const idTypeEl = kycForm.querySelector('select[name="id_type"]');
                    const idNumberEl = kycForm.querySelector('input[name="id_number"]');

                    if (!idTypeEl || !idNumberEl) return;

                    const idType = idTypeEl.value;
                    const idNumber = idNumberEl.value.trim();

                    let isValid = true;
                    let message = '';

                    if (idType === 'CNIC') {
                        const cleaned = idNumber.replace(/-/g, '');
                        if (!/^\d{13}$/.test(cleaned)) {
                            isValid = false;
                            message = 'CNIC must be exactly 13 digits (e.g. 4210112345671).';
                        }
                    } else if (idType === 'Passport') {
                        if (!/^[A-Za-z]{1,2}\d{7,8}$/.test(idNumber)) {
                            isValid = false;
                            message =
                                'Passport number must start with 1-2 letters followed by 7-8 digits (e.g. AB1234567).';
                        }
                    } else if (idType === 'Driving License') {
                        if (!/^[A-Za-z0-9-]{8,15}$/.test(idNumber)) {
                            isValid = false;
                            message = 'Driving License number must be 8-15 alphanumeric characters.';
                        }
                    }

                    if (!isValid) {
                        e.preventDefault();
                        try {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Invalid ID Number',
                                    text: message,
                                });
                            } else {
                                alert(message);
                            }
                        } catch (alertErr) {
                            console.error('Alert display error:', alertErr);
                        }
                    }
                } catch (err) {
                    console.error('ID validation error:', err);
                }
            });

        })();
    </script>
@endpush
