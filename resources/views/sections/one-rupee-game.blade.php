
    <section class="one-rupee-game pb-8 md:pb-12">
        <div class="container">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left --}}
                <div>
                    <small class="uppercase tracking-[3px] text-brand-primary">
                        Lucky Draw
                    </small>
                    <h1 class="mt-4">
                        Win Big With
                        <span class="text-brand-primary text-inherit">
                            Just Rs.1
                        </span>
                    </h1>
                    <p class="mt-6">
                        Participate in our exclusive One Rupee Lucky Draw and get
                        a chance to win amazing prizes including smartphones,
                        motorcycles, laptops and much more.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-6">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-ticket text-brand-primary"></i>
                            <span>Entry Fee Rs.1</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-user-group text-brand-primary"></i>
                            <span>28,451 Players Joined</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-trophy text-brand-primary"></i>
                            <span>1 Lucky Winner</span>
                        </div>
                    </div>
                    <a href="{{ route('participate') }}" class="btn-primary mt-10">
                        Participate Now
                    </a>
                </div>

                {{-- Right --}}
                <div>
                    <img src="{{ asset('images/draw/draw.png') }}" class="w-full max-w-lg mx-auto" alt="Prize">
                </div>

            </div>

        </div>

    </section>
