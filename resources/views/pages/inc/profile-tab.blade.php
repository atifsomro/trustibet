<section class="profile">
    <div class="container">
        <div class="grid xl:grid-cols-12 gap-6">
            {{-- Profile Card --}}
            <div class="xl:col-span-4">
                <div class="rounded-3xl border border-brand-border bg-brand-surface text-center">
                    <div class="relative inline-block">
                        <img id="profilePreview" src="{{ auth()->user()->avatar ??  asset('images/profile/avatar.png') }}" alt="Profile"
                            class="w-20 h-20 md:w-30 md:h-30 rounded-full object-cover border-4 border-brand-primary">
                        <label for="profileImage"
                            class="absolute bottom-2 right-2 flex h-6 w-6 md:h-10 md:w-10 cursor-pointer items-center justify-center rounded-full bg-brand-primary shadow-lg transition hover:scale-110">
                            <i class="fa-solid fa-camera text-white"></i>
                        </label>
                        <input type="file" id="profileImage" accept="image/png,image/jpeg,image/webp" class="hidden">
                    </div>
                    <h3 class="mt-3">{{ auth()->user()->name }}</h3>
                    <p class="mt-2 opacity-70">
                        Premium Member
                    </p>
                    <div class="mt-8 text-left">
                        <div class="mb-2 flex justify-between">
                            <span class="text-sm">Profile Completion</span>
                            <span class="text-sm font-semibold text-brand-primary">80%</span>
                        </div>
                        <div class="h-3 w-full overflow-hidden rounded-full bg-brand-dark">
                            <div id="p_progress" class="h-full w-4/5 rounded-full bg-brand-primary"></div>
                        </div>
                    </div>
                    <div class="mt-8 space-y-4 rounded-2xl border border-brand-border bg-brand-dark p-5 text-left">
                        <div class="flex items-center justify-between">
                            <span>Email Status</span>
                            <span class="font-medium text-green-500">
                                <i class="fa-solid fa-circle-check mr-1"></i>Verified
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span>Phone Status</span>
                            <span class="font-medium text-green-500">
                                <i class="fa-solid fa-circle-check mr-1"></i>Verified
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span>KYC Status</span>
                            <span class="font-medium text-yellow-500">
                                <i class="fa-solid fa-clock mr-1"></i>Pending
                            </span>
                        </div>
                    </div>
                    <p class="mt-6 text-xs opacity-60">
                        Upload JPG, PNG or WEBP image.<br>
                        Maximum file size: 2MB.
                    </p>
                </div>
            </div>
            {{-- Personal Information --}}
            <div class="xl:col-span-8">
                <div class="rounded-3xl border border-brand-border bg-brand-surface p-4 md:p-8">
                    <div class="mb-8">
                        <h3>Personal Information</h3>
                        <p class="mt-2">
                            Your personal information is protected and cannot be modified directly. If you need to
                            update your details, please contact TrustiBet Support.
                        </p>
                    </div>
                    <form action="">
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block">Full Name</label>
                                <input type="text" value="{{ auth()->user()->name }}" disabled
                                    class="w-full cursor-not-allowed rounded-xl border border-brand-border bg-brand-dark px-4 py-3 opacity-70">
                            </div>
                            <div>
                                <label class="mb-2 block">Username</label>
                                <input type="text" value="{{ auth()->user()->username }}" disabled
                                    class="w-full cursor-not-allowed rounded-xl border border-brand-border bg-brand-dark px-4 py-3 opacity-70">
                            </div>
                            <div>
                                <label class="mb-2 block">Email Address</label>
                                <input type="email" value="{{ auth()->user()->email }}" disabled
                                    class="w-full cursor-not-allowed rounded-xl border border-brand-border bg-brand-dark px-4 py-3 opacity-70">
                            </div>

                            <div>
                                <label class="mb-2 block">Phone Number</label>
                                <input type="text" value="{{ auth()->user()->phone ?? "" }}" disabled
                                    class="w-full cursor-not-allowed rounded-xl border border-brand-border bg-brand-dark px-4 py-3 opacity-70">
                            </div>

                            <div>
                                <label class="mb-2 block">Country</label>
                                <input type="text" value="{{ auth()->user()->country()->name ?? "" }}" disabled
                                    class="w-full cursor-not-allowed rounded-xl border border-brand-border bg-brand-dark px-4 py-3 opacity-70">
                            </div>

                            <div>
                                <label class="mb-2 block">Date of Birth</label>
                                <input type="text" value="" disabled
                                    class="w-full cursor-not-allowed rounded-xl border border-brand-border bg-brand-dark px-4 py-3 opacity-70">
                            </div>

                        </div>
                    </form>
                    <div class="mt-8 rounded-2xl border border-brand-border bg-brand-dark p-5">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-info mt-1 text-brand-primary"></i>
                            <div>
                                <h5>Need to update your details?</h5>
                                <p class="mt-2 text-sm opacity-70">
                                    For your account security, profile information cannot be edited directly. If you
                                    need to change your email, phone number or other details, please contact TrustiBet
                                    Support.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
