<!-- Premium Sticky Header -->
<header x-data="{ scrolled: false, mobileOpen: false }" @scroll.window="scrolled = (window.pageYOffset > 50)"
    :class="{ 'glass shadow-lg': scrolled, 'bg-transparent': !scrolled }"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <!-- Top Bar -->
    <div class="bg-primary-800 text-white py-2 hidden md:block">
        <div class="container mx-auto px-4 flex justify-between items-center text-sm">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    {{ institution_phone() ?? '০১XXXXXXXXX' }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ institution_email() ?? 'info@example.com' }}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('portal') }}" class="flex items-center gap-1 hover:text-gold-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    পোর্টাল লগইন
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if(institution_logo())
                    <img src="{{ institution_logo() }}" alt="Logo"
                        class="h-14 w-14 rounded-full shadow-lg border-2 border-white">
                @else
                    <div
                        class="h-14 w-14 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                        🕌
                    </div>
                @endif
                <div class="hidden sm:block">
                    <h1 class="text-lg font-bold" :class="scrolled ? 'text-primary-800' : 'text-white'">
                        {{ institution_name() ?? 'মাদরাসা নাম' }}
                    </h1>
                    <p class="text-xs" :class="scrolled ? 'text-gray-600' : 'text-primary-100'">
                        ইসলামী শিক্ষার আলোকিত প্রতিষ্ঠান
                    </p>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ route('home') }}" class="nav-link px-4 py-2 rounded-lg font-medium transition-colors"
                    :class="scrolled ? 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' : 'text-white hover:bg-white/10'">
                    হোম
                </a>

                <!-- About Dropdown -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                    <button class="nav-link px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1"
                        :class="scrolled ? 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' : 'text-white hover:bg-white/10'">
                        পরিচিতি
                        <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition
                        class="absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50">
                        <a href="{{ route('about') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">প্রতিষ্ঠান
                            পরিচিতি</a>
                        <a href="{{ route('about.history') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">ইতিহাস</a>
                        <a href="{{ route('about.mission') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">লক্ষ্য ও
                            উদ্দেশ্য</a>
                        <a href="{{ route('about.committee') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">পরিচালনা
                            কমিটি</a>
                        <a href="{{ route('about.teachers') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">শিক্ষক
                            তালিকা</a>
                        <a href="{{ route('about.staff') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">কর্মচারী
                            তালিকা</a>
                    </div>
                </div>

                <!-- Academic Dropdown -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                    <button class="nav-link px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1"
                        :class="scrolled ? 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' : 'text-white hover:bg-white/10'">
                        একাডেমিক
                        <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition
                        class="absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50">
                        <a href="{{ route('departments') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">বিভাগসমূহ</a>
                        <a href="{{ route('routine') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">ক্লাস
                            রুটিন</a>
                        <a href="{{ route('academic.calendar') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">একাডেমিক
                            ক্যালেন্ডার</a>
                        <a href="{{ route('results') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">ফলাফল</a>
                        <a href="{{ route('downloads') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">ডাউনলোড</a>
                    </div>
                </div>

                <!-- Admission Dropdown -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                    <button class="nav-link px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1"
                        :class="scrolled ? 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' : 'text-white hover:bg-white/10'">
                        ভর্তি
                        <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition
                        class="absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50">
                        <a href="{{ route('admission') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">ভর্তি
                            তথ্য</a>
                        <a href="{{ route('admission.eligibility') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">আবেদনের
                            যোগ্যতা</a>
                        <a href="{{ route('admission.fees') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">ফি
                            স্ট্রাকচার</a>
                        <a href="{{ route('admission.apply') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">অনলাইন
                            আবেদন</a>
                    </div>
                </div>

                <!-- Gallery Dropdown -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                    <button class="nav-link px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1"
                        :class="scrolled ? 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' : 'text-white hover:bg-white/10'">
                        গ্যালারি
                        <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition
                        class="absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50">
                        <a href="{{ route('gallery') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">ফটো
                            গ্যালারি</a>
                        <a href="{{ route('gallery.videos') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-700">ভিডিও
                            গ্যালারি</a>
                    </div>
                </div>

                <a href="{{ route('news') }}" class="nav-link px-4 py-2 rounded-lg font-medium transition-colors"
                    :class="scrolled ? 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' : 'text-white hover:bg-white/10'">
                    সংবাদ
                </a>

                <a href="{{ route('contact') }}" class="nav-link px-4 py-2 rounded-lg font-medium transition-colors"
                    :class="scrolled ? 'text-gray-700 hover:bg-primary-50 hover:text-primary-700' : 'text-white hover:bg-white/10'">
                    যোগাযোগ
                </a>

                <!-- Donate Button -->
                <a href="{{ route('donate') }}"
                    class="ml-2 px-5 py-2 bg-gradient-to-r from-gold-400 to-gold-600 text-white rounded-full font-medium shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                    অনুদান দিন
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="$dispatch('toggle-mobile-menu')" class="lg:hidden p-2 rounded-lg"
                :class="scrolled ? 'text-gray-700' : 'text-white'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>
</header>