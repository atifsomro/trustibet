<section class="py-16">
    <div class="container">

        {{-- Section Heading --}}
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2>Top Game Providers</h2>
            <p class="mt-3">
                Play premium casino games from the world's most trusted software providers.
            </p>
        </div>

        {{-- Providers Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-5">

            @php
                $providers = [
                    [
                        'name' => 'Pragmatic Play',
                        'logo' => 'https://dummyimage.com/220x90/0f172a/38bdf8&text=Pragmatic',
                    ],
                    ['name' => 'Evolution', 'logo' => 'https://dummyimage.com/220x90/0f172a/38bdf8&text=Evolution'],
                    ['name' => 'NetEnt', 'logo' => 'https://dummyimage.com/220x90/0f172a/38bdf8&text=NetEnt'],
                    ['name' => 'Playtech', 'logo' => 'https://dummyimage.com/220x90/0f172a/38bdf8&text=Playtech'],
                    ['name' => 'Microgaming', 'logo' => 'https://dummyimage.com/220x90/0f172a/38bdf8&text=Microgaming'],
                    ['name' => 'BGaming', 'logo' => 'https://dummyimage.com/220x90/0f172a/38bdf8&text=BGaming'],
                    ['name' => 'Hacksaw Gaming', 'logo' => 'https://dummyimage.com/220x90/0f172a/38bdf8&text=Hacksaw'],
                    ['name' => 'Nolimit City', 'logo' => 'https://dummyimage.com/220x90/0f172a/38bdf8&text=Nolimit'],
                ];
            @endphp

            @foreach ($providers as $provider)
                <div
                    class="group bg-brand-surface border border-brand-border rounded-2xl p-6 transition-all duration-300 hover:border-brand-primary hover:-translate-y-1">

                    <div class="h-16 flex items-center justify-center">
                        <img
                            src="{{ asset('images/providers/pragmatic.webp') }}"
                            alt="Pragmatic Play"
                            loading="lazy"
                            width="180"
                            height="70"
                            class="max-h-12 object-contain">
                    </div>

                    <div class="mt-5 text-center">
                        <h6>{{ $provider['name'] }}</h6>

                        <span
                            class="mt-2 inline-flex items-center rounded-full px-3 py-1 bg-brand-dark border border-brand-border">
                            Official Provider
                        </span>
                    </div>

                </div>
            @endforeach

        </div>

    </div>
</section>
