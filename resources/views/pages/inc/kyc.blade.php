<div class="kyc">
    <div class="grid xl:grid-cols-12 gap-6">
        {{-- Left --}}
        <div class="xl:col-span-8">
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-2">
                <div class="mb-8">
                    <h3>KYC Verification</h3>
                    <p class="mt-2 opacity-70">
                        Complete your identity verification to unlock withdrawals and premium features.
                    </p>
                </div>
                <form>
                    {{-- Personal Information --}}
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="mb-2 block">
                                Full Name
                            </label>
                            <input type="text" placeholder="Enter your full name"
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                        </div>
                        <div>
                            <label class="mb-2 block">
                                Date of Birth
                            </label>
                            <input type="date"
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                        </div>
                        <div>
                            <label class="mb-2 block">
                                Country
                            </label>
                            <input type="text" value="Pakistan" disabled
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 opacity-70">
                        </div>
                        <div>
                            <label class="mb-2 block">
                                ID Type
                            </label>
                            <select
                                class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                                <option>CNIC</option>
                                <option>Passport</option>
                                <option>Driving License</option>
                            </select>
                        </div>
                    </div>
                    {{-- Uploads --}}
                    <div class="mt-4 sm:mt-10">
                        <h4>Upload Documents</h4>
                        <div class="grid lg:grid-cols-3 gap-6 mt-6">
                            {{-- Front --}}
                            <label
                                class="cursor-pointer rounded-2xl border-2 border-dashed border-brand-border p-8 text-center hover:border-brand-primary">
                                <i class="fa-solid fa-id-card text-xl sm:text-5xl text-brand-primary"></i>
                                <h5 class="mt-5">
                                    Front Side
                                </h5>
                                <p class="mt-2 text-sm opacity-70">
                                    Upload front side of your ID.
                                </p>
                                <input type="file" class="hidden">
                            </label>
                            {{-- Back --}}
                            <label
                                class="cursor-pointer rounded-2xl border-2 border-dashed border-brand-border p-8 text-center hover:border-brand-primary">
                                <i class="fa-solid fa-address-card text-xl sm:text-5xl text-brand-primary"></i>
                                <h5 class="mt-5">
                                    Back Side
                                </h5>
                                <p class="mt-2 text-sm opacity-70">
                                    Upload back side of your ID.
                                </p>
                                <input type="file" class="hidden">
                            </label>
                            {{-- Selfie --}}
                            <label
                                class="cursor-pointer rounded-2xl border-2 border-dashed border-brand-border p-8 text-center hover:border-brand-primary">
                                <i class="fa-solid fa-camera text-xl sm:text-5xl text-brand-primary"></i>
                                <h5 class="mt-5">
                                    Selfie
                                </h5>
                                <p class="mt-2 text-sm opacity-70">
                                    Upload a clear selfie.
                                </p>
                                <input type="file" class="hidden">
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
                                <input type="text" placeholder="Street address"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                            </div>
                            <div>
                                <label class="mb-2 block">
                                    City
                                </label>
                                <input type="text" placeholder="Enter city"
                                    class="w-full rounded-xl border border-brand-border bg-brand-dark px-4 py-3 outline-none focus:border-brand-primary">
                            </div>
                        </div>
                    </div>
                    {{-- Terms --}}
                    <div class="mt-4 sm:mt-10 space-y-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox">
                            <span class="text-[10px] sm:text-sm">
                                I confirm that all submitted documents belong to me.
                            </span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox">
                            <span class="text-[10px] sm:text-sm">
                                I agree to the TrustiBet KYC Policy.
                            </span>
                        </label>
                    </div>
                    <button class="btn-primary mt-4 sm:mt-10">
                        <i class="fa-solid fa-paper-plane mr-2"></i>
                        Submit Verification
                    </button>

                </form>

            </div>

        </div>

        {{-- Right --}}
        <div class="xl:col-span-4">

            {{-- Status --}}
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-8">
                <div class="sm:flex flex-col sm:text-start text-center items-center gap-3">
                    <i class="fa-solid fa-shield-halved text-xlsm:text-3xl text-yellow-500"></i>
                    <div>
                        <h4>KYC Status</h4>
                        <p class="text-yellow-500 mt-1">
                            Pending
                        </p>
                    </div>
                </div>
                <p class="mt-3 sm:mt-6 opacity-70 sm:text-start text-center">
                    Your submitted documents will be reviewed within 24–48 hours.
                </p>
            </div>
            {{-- Timeline --}}
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-8 mt-3 sm:mt-6">
                <h4>Verification Steps</h4>
                <div class="mt-6 space-y-6">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-green-500"></i>
                        <span>Submit Documents</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-clock text-yellow-500"></i>
                        <span>Under Review</span>
                    </div>
                    <div class="flex items-center gap-3 opacity-50">
                        <i class="fa-solid fa-circle"></i>
                        <span>Approved</span>
                    </div>
                </div>
            </div>
            {{-- Guidelines --}}
            <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 sm:p-8 mt-3 sm:mt-6">
                <h4>Guidelines</h4>
                <ul class="mt-3 sm:mt-6 space-y-3 text-[10px] md:text-xl lg:text-2xl">
                    <li>✅ Upload clear images.</li>
                    <li>✅ All corners must be visible.</li>
                    <li>✅ No edited or cropped documents.</li>
                    <li>✅ Selfie must match your ID.</li>
                    <li>✅ JPG, PNG & WEBP supported.</li>
                    <li>✅ Maximum file size: 5MB.</li>
                </ul>

            </div>

        </div>

    </div>
</div>
