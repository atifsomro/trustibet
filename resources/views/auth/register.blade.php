@extends('layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <section class="register py-6 sm:py-10 md:py-16 lg:py-20">
        <div class="container">

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div
                class="register__wrapper relative overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-[0_0_45px_rgba(34,197,94,.12),0_0_70px_rgba(249,115,22,.08)]">

                <div class="h-1.5 bg-gradient-to-r from-green-500 via-orange-400 to-orange-500"></div>

                <div class="grid lg:grid-cols-2">

                    {{-- Left Side --}}
                    <div
                        class="register__content hidden lg:flex flex-col justify-center p-14 bg-[radial-gradient(circle_at_top_right,rgba(34,197,94,.10),transparent_50%),radial-gradient(circle_at_bottom_left,rgba(249,115,22,.10),transparent_50%)]">

                        <span
                            class="inline-flex w-fit items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-4 py-2 text-sm font-medium uppercase tracking-[3px] text-green-500">
                            <i class="fa-solid fa-user-plus"></i>
                            Join Our Community
                        </span>

                        <h2 class="mt-4">
                            Create Your
                            <span class="bg-gradient-to-r from-green-500 to-orange-500 bg-clip-text text-transparent">
                                Gaming Account
                            </span>
                        </h2>

                        <p class="mt-6">
                            Register today to access exciting casino games, exclusive
                            promotions, secure payments, and an unforgettable gaming
                            experience.
                        </p>

                        <div class="mt-10 space-y-5">

                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-green-500/10">
                                    <i class="fa-solid fa-circle-check text-green-500"></i>
                                </span>
                                <span>Quick Registration</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500/10">
                                    <i class="fa-solid fa-shield-halved text-orange-400"></i>
                                </span>
                                <span>Safe & Secure Platform</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-green-500/10">
                                    <i class="fa-solid fa-gift text-green-500"></i>
                                </span>
                                <span>Exclusive Member Rewards</span>
                            </div>

                        </div>
                    </div>

                    {{-- Right Side --}}
                    <div class="register__form p-4 sm:p-8 md:p-12 lg:p-16">

                        <h3 class="text-green-500">
                            Create Account
                        </h3>

                        <p class="mt-2">
                            Fill in your information to create your account.
                        </p>

                        <form id="registerForm" action="{{ route('auth.register') }}" method="POST" class="mt-4 sm:mt-8">

                            @csrf

                            <input type="hidden" name="referral_code" value="{{ $referralCode ?? '' }}">

                            @if (!empty($referralCode))
                                <div class="mb-4 rounded-xl border border-orange-500/30 bg-orange-500/10 px-4 py-3 text-sm">

                                    Signing up with referral code

                                    <span class="font-semibold text-orange-400 tracking-widest">
                                        {{ $referralCode }}
                                    </span>

                                </div>
                            @endif

                            <div class="grid gap-3 sm:gap-6 md:grid-cols-2">

                                {{-- Full Name --}}
                                <div>

                                    <label for="fname" class="mb-2 block">
                                        Full Name
                                    </label>

                                    <input type="text" id="fname" name="name" value="{{ old('name') }}"
                                        placeholder="Enter your full name"
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
                                        required>

                                    <small id="fnameError" class="mt-2 block text-[10px] text-red-500">

                                        @error('name')
                                            {{ $message }}
                                        @enderror

                                    </small>

                                </div>

                                {{-- Username --}}
                                <div>

                                    <label for="username" class="mb-2 block">
                                        Username
                                    </label>

                                    <input type="text" id="username" value="{{ old('username') }}" name="username"
                                        placeholder="Choose a username"
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
                                        required>

                                    <small id="unameError" class="mt-2 block text-[10px] text-red-500">

                                        @error('username')
                                            {{ $message }}
                                        @enderror

                                    </small>

                                </div>

                            </div>

                            <div class="grid gap-3 sm:gap-6 md:grid-cols-2 mt-3 sm:mt-6">

                                {{-- Email --}}
                                <div>

                                    <label for="email" class="mb-2 block">
                                        Email Address
                                    </label>

                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        placeholder="Enter your email"
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
                                        required>

                                    <small id="emailError" class="mt-2 block text-[10px] text-red-500">

                                        @error('email')
                                            {{ $message }}
                                        @enderror

                                    </small>

                                </div>

                                {{-- Country --}}
                                <div>

                                    <label for="country_id" class="mb-2 block">
                                        Country
                                    </label>

                                    <select id="country_id" name="country_id"
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
                                        required>

                                        <option value="" selected disabled>
                                            Select Country
                                        </option>

                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                    <small id="countryError" class="mt-2 block text-[10px] text-red-500">

                                        @error('country_id')
                                            {{ $message }}
                                        @enderror

                                    </small>

                                </div>

                            </div>

                            <div class="grid gap-3 sm:gap-6 md:grid-cols-2 mt-3 sm:mt-6">

                                {{-- Phone Number --}}
                                <div>

                                    <label for="phone" class="mb-2 block">
                                        Phone Number
                                    </label>

                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                        placeholder="Select country first"
                                        class="w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
                                        required>

                                    <small id="phoneError" class="mt-2 block text-[10px] text-red-500">

                                        @error('phone')
                                            {{ $message }}
                                        @enderror

                                    </small>

                                </div>

                                {{-- Password --}}
                                <div>

                                    <label for="password" class="mb-2 block">
                                        Password
                                    </label>

                                    <div class="relative">

                                        <input type="password" name="password" id="password"
                                            placeholder="Enter your password"
                                            class="password-field w-full rounded-xl border border-green-500/30 bg-brand-dark px-4 py-3 outline-none transition focus:border-green-500 focus:shadow-[0_0_18px_rgba(34,197,94,.18)]"
                                            required>

                                        <button type="button" class="toggle-password">

                                            <i class="fa-regular fa-eye password-icon"></i>

                                        </button>

                                    </div>

                                    <small id="passwordError" class="mt-2 block text-[10px] text-red-500">

                                        @error('password')
                                            {{ $message }}
                                        @enderror

                                    </small>

                                </div>

                            </div>

                            {{-- Password strength --}}
                            <div class="mt-3">

                                <div class="h-2 overflow-hidden rounded-full bg-brand-border">

                                    <div id="passwordStrengthBar"
                                        class="h-full w-0 rounded-full transition-all duration-300">
                                    </div>

                                </div>

                                <small id="passwordStrengthText" class="mt-2 block">
                                </small>

                            </div>

                            <div class="flex flex-col items-center justify-center gap-2">

                                <button type="submit" class="btn-orange mt-4 sm:mt-8 w-full justify-center">

                                    <i class="fa-solid fa-user-plus mr-2"></i>
                                    Register

                                </button>

                                <span class="text-gray-400">
                                    Or register with
                                </span>

                                <a href="{{ route('auth.google') }}"
                                    class="w-10 h-10 p-1 bg-white flex rounded items-center justify-center mx-auto transition hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(34,197,94,.3)]"
                                    title="Register With Google">

                                    <img src="{{ asset('images/google/google.svg') }}" class="w-full" alt="google icon">

                                </a>

                            </div>

                        </form>

                        <p class="mt-3 sm:mt-6 text-center">

                            Already have an account?

                            <a href="{{ route('auth.login') }}"
                                class="font-semibold text-green-500 hover:text-green-400 transition">
                                Login
                            </a>

                        </p>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const countrySelect = document.getElementById('country_id');
            const phoneInput = document.getElementById('phone');

            const countryDialCodes = {
                'Afghanistan': '+93',
                'Albania': '+355',
                'Algeria': '+213',
                'Andorra': '+376',
                'Angola': '+244',
                'Antigua and Barbuda': '+1',
                'Argentina': '+54',
                'Armenia': '+374',
                'Australia': '+61',
                'Austria': '+43',
                'Azerbaijan': '+994',
                'Bahamas': '+1',
                'Bahrain': '+973',
                'Bangladesh': '+880',
                'Barbados': '+1',
                'Belarus': '+375',
                'Belgium': '+32',
                'Belize': '+501',
                'Benin': '+229',
                'Bhutan': '+975',
                'Bolivia': '+591',
                'Bosnia and Herzegovina': '+387',
                'Botswana': '+267',
                'Brazil': '+55',
                'Brunei': '+673',
                'Bulgaria': '+359',
                'Burkina Faso': '+226',
                'Burundi': '+257',
                'Cambodia': '+855',
                'Cameroon': '+237',
                'Canada': '+1',
                'Cape Verde': '+238',
                'Central African Republic': '+236',
                'Chad': '+235',
                'Chile': '+56',
                'China': '+86',
                'Colombia': '+57',
                'Comoros': '+269',
                'Congo': '+242',
                'Costa Rica': '+506',
                'Croatia': '+385',
                'Cuba': '+53',
                'Cyprus': '+357',
                'Czech Republic': '+420',
                'Denmark': '+45',
                'Djibouti': '+253',
                'Dominica': '+1',
                'Dominican Republic': '+1',
                'Ecuador': '+593',
                'Egypt': '+20',
                'El Salvador': '+503',
                'Equatorial Guinea': '+240',
                'Eritrea': '+291',
                'Estonia': '+372',
                'Eswatini': '+268',
                'Ethiopia': '+251',
                'Fiji': '+679',
                'Finland': '+358',
                'France': '+33',
                'Gabon': '+241',
                'Gambia': '+220',
                'Georgia': '+995',
                'Germany': '+49',
                'Ghana': '+233',
                'Greece': '+30',
                'Grenada': '+1',
                'Guatemala': '+502',
                'Guinea': '+224',
                'Guinea-Bissau': '+245',
                'Guyana': '+592',
                'Haiti': '+509',
                'Honduras': '+504',
                'Hungary': '+36',
                'Iceland': '+354',
                'India': '+91',
                'Indonesia': '+62',
                'Iran': '+98',
                'Iraq': '+964',
                'Ireland': '+353',
                'Italy': '+39',
                'Jamaica': '+1',
                'Japan': '+81',
                'Jordan': '+962',
                'Kazakhstan': '+7',
                'Kenya': '+254',
                'Kiribati': '+686',
                'Kuwait': '+965',
                'Kyrgyzstan': '+996',
                'Laos': '+856',
                'Latvia': '+371',
                'Lebanon': '+961',
                'Lesotho': '+266',
                'Liberia': '+231',
                'Libya': '+218',
                'Liechtenstein': '+423',
                'Lithuania': '+370',
                'Luxembourg': '+352',
                'Madagascar': '+261',
                'Malawi': '+265',
                'Malaysia': '+60',
                'Maldives': '+960',
                'Mali': '+223',
                'Malta': '+356',
                'Marshall Islands': '+692',
                'Mauritania': '+222',
                'Mauritius': '+230',
                'Mexico': '+52',
                'Micronesia': '+691',
                'Moldova': '+373',
                'Monaco': '+377',
                'Mongolia': '+976',
                'Montenegro': '+382',
                'Morocco': '+212',
                'Mozambique': '+258',
                'Myanmar': '+95',
                'Namibia': '+264',
                'Nauru': '+674',
                'Nepal': '+977',
                'Netherlands': '+31',
                'New Zealand': '+64',
                'Nicaragua': '+505',
                'Niger': '+227',
                'Nigeria': '+234',
                'North Korea': '+850',
                'North Macedonia': '+389',
                'Norway': '+47',
                'Oman': '+968',
                'Pakistan': '+92',
                'Palau': '+680',
                'Palestine': '+970',
                'Panama': '+507',
                'Papua New Guinea': '+675',
                'Paraguay': '+595',
                'Peru': '+51',
                'Philippines': '+63',
                'Poland': '+48',
                'Portugal': '+351',
                'Qatar': '+974',
                'Romania': '+40',
                'Russia': '+7',
                'Rwanda': '+250',
                'Saint Kitts and Nevis': '+1',
                'Saint Lucia': '+1',
                'Saint Vincent and the Grenadines': '+1',
                'Samoa': '+685',
                'San Marino': '+378',
                'Sao Tome and Principe': '+239',
                'Saudi Arabia': '+966',
                'Senegal': '+221',
                'Serbia': '+381',
                'Seychelles': '+248',
                'Sierra Leone': '+232',
                'Singapore': '+65',
                'Slovakia': '+421',
                'Slovenia': '+386',
                'Solomon Islands': '+677',
                'Somalia': '+252',
                'South Africa': '+27',
                'South Korea': '+82',
                'South Sudan': '+211',
                'Spain': '+34',
                'Sri Lanka': '+94',
                'Sudan': '+249',
                'Suriname': '+597',
                'Sweden': '+46',
                'Switzerland': '+41',
                'Syria': '+963',
                'Taiwan': '+886',
                'Tajikistan': '+992',
                'Tanzania': '+255',
                'Thailand': '+66',
                'Timor-Leste': '+670',
                'Togo': '+228',
                'Tonga': '+676',
                'Trinidad and Tobago': '+1',
                'Tunisia': '+216',
                'Turkey': '+90',
                'Turkmenistan': '+993',
                'Tuvalu': '+688',
                'Uganda': '+256',
                'Ukraine': '+380',
                'United Arab Emirates': '+971',
                'United Kingdom': '+44',
                'United States': '+1',
                'Uruguay': '+598',
                'Uzbekistan': '+998',
                'Vanuatu': '+678',
                'Vatican City': '+39',
                'Venezuela': '+58',
                'Vietnam': '+84',
                'Yemen': '+967',
                'Zambia': '+260',
                'Zimbabwe': '+263'
            };

            function normalizeCountryName(name) {
                return name
                    .trim()
                    .toLowerCase()
                    .replace(/\s+/g, ' ')
                    .replace(/[’']/g, "'");
            }

            const normalizedDialCodes = {};

            Object.keys(countryDialCodes).forEach(function(country) {
                normalizedDialCodes[normalizeCountryName(country)] =
                    countryDialCodes[country];
            });

            function getDialCode() {

                if (!countrySelect || !countrySelect.value) {
                    return '';
                }

                const selectedOption =
                    countrySelect.options[countrySelect.selectedIndex];

                if (!selectedOption) {
                    return '';
                }

                const countryName =
                    normalizeCountryName(selectedOption.textContent);

                return normalizedDialCodes[countryName] || '';
            }

            function setCountryCode() {

                const dialCode = getDialCode();

                if (!dialCode) {
                    phoneInput.placeholder = 'Select country first';
                    return;
                }

                phoneInput.placeholder = 'Enter your phone number';

                let currentValue = phoneInput.value.trim();

                if (!currentValue) {
                    phoneInput.value = dialCode;
                    return;
                }

                const allDialCodes = Object.values(countryDialCodes)
                    .sort(function(a, b) {
                        return b.length - a.length;
                    });

                let detectedCode = '';

                for (const code of allDialCodes) {
                    if (currentValue.startsWith(code)) {
                        detectedCode = code;
                        break;
                    }
                }

                if (detectedCode) {

                    const numberPart = currentValue
                        .substring(detectedCode.length)
                        .replace(/\D/g, '');

                    phoneInput.value = dialCode + numberPart;

                } else {

                    const numberPart = currentValue.replace(/\D/g, '');

                    const numericDialCode = dialCode.replace('+', '');

                    let cleanNumber = numberPart;

                    if (cleanNumber.startsWith(numericDialCode)) {
                        cleanNumber = cleanNumber.substring(
                            numericDialCode.length
                        );
                    }

                    phoneInput.value = dialCode + cleanNumber;
                }
            }

            countrySelect.addEventListener('change', function() {

                setCountryCode();

                setTimeout(function() {

                    phoneInput.focus();

                    phoneInput.setSelectionRange(
                        phoneInput.value.length,
                        phoneInput.value.length
                    );

                }, 50);
            });

            phoneInput.addEventListener('input', function() {

                const dialCode = getDialCode();

                if (!dialCode) {
                    phoneInput.value =
                        phoneInput.value.replace(/\D/g, '');

                    return;
                }

                let value = phoneInput.value;

                if (!value.startsWith(dialCode)) {

                    const numberPart =
                        value.replace(/\D/g, '');

                    const numericDialCode =
                        dialCode.replace('+', '');

                    let cleanNumber = numberPart;

                    if (cleanNumber.startsWith(numericDialCode)) {
                        cleanNumber =
                            cleanNumber.substring(
                                numericDialCode.length
                            );
                    }

                    phoneInput.value =
                        dialCode + cleanNumber;

                    return;
                }

                const numberPart =
                    value
                    .substring(dialCode.length)
                    .replace(/\D/g, '');

                phoneInput.value =
                    dialCode + numberPart;
            });

            phoneInput.addEventListener('keydown', function(event) {

                const dialCode = getDialCode();

                if (!dialCode) {
                    return;
                }

                const start = phoneInput.selectionStart;

                if (
                    (event.key === 'Backspace' ||
                        event.key === 'Delete') &&
                    start <= dialCode.length
                ) {
                    event.preventDefault();

                    phoneInput.setSelectionRange(
                        dialCode.length,
                        dialCode.length
                    );
                }

                if (
                    event.key === 'ArrowLeft' &&
                    start <= dialCode.length
                ) {
                    event.preventDefault();

                    phoneInput.setSelectionRange(
                        dialCode.length,
                        dialCode.length
                    );
                }
            });

            phoneInput.addEventListener('focus', function() {

                const dialCode = getDialCode();

                if (!dialCode) {
                    return;
                }

                if (!phoneInput.value.startsWith(dialCode)) {
                    phoneInput.value = dialCode;
                }

                setTimeout(function() {

                    const length = phoneInput.value.length;

                    phoneInput.setSelectionRange(
                        length,
                        length
                    );

                }, 0);
            });

            if (countrySelect.value) {
                setCountryCode();
            }

        });
    </script>
@endpush
