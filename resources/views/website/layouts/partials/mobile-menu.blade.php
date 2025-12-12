<!-- Mobile Menu Overlay -->
<div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open" x-show="open"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
    class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    <!-- Mobile Menu Panel -->
    <div @click.stop x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="absolute left-0 top-0 bottom-0 w-80 bg-white shadow-xl overflow-y-auto">
        <!-- Header -->
        <div class="gradient-primary text-white p-6">
            <div class="flex items-center gap-3">
                @if(institution_logo())
                    <img src="{{ institution_logo() }}" alt="Logo" class="h-12 w-12 rounded-full">
                @else
                    <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center text-xl">
                        🕌
                    </div>
                @endif
                <div>
                    <h2 class="font-bold">{{ institution_name() ?? 'মাদরাসা নাম' }}</h2>
                    <p class="text-xs text-primary-200">ইসলামী শিক্ষার আলোকিত প্রতিষ্ঠান</p>
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        <nav class="p-4">
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                হোম
            </a>

            <!-- About Accordion -->
            <div x-data="{ expanded: false }">
                <button @click="expanded = !expanded"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        পরিচিতি
                    </span>
                    <svg class="w-4 h-4 transition-transform" :class="expanded && 'rotate-180'" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" x-collapse class="pl-10 space-y-1">
                    <a href="{{ route('about') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">প্রতিষ্ঠান পরিচিতি</a>
                    <a href="{{ route('about.history') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">ইতিহাস</a>
                    <a href="{{ route('about.mission') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">লক্ষ্য ও উদ্দেশ্য</a>
                    <a href="{{ route('about.teachers') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">শিক্ষক তালিকা</a>
                </div>
            </div>

            <!-- Academic Accordion -->
            <div x-data="{ expanded: false }">
                <button @click="expanded = !expanded"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        একাডেমিক
                    </span>
                    <svg class="w-4 h-4 transition-transform" :class="expanded && 'rotate-180'" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" x-collapse class="pl-10 space-y-1">
                    <a href="{{ route('departments') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">বিভাগসমূহ</a>
                    <a href="{{ route('routine') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">ক্লাস রুটিন</a>
                    <a href="{{ route('academic.calendar') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">একাডেমিক ক্যালেন্ডার</a>
                    <a href="{{ route('results') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">ফলাফল</a>
                    <a href="{{ route('downloads') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">ডাউনলোড</a>
                </div>
            </div>

            <!-- Admission Accordion -->
            <div x-data="{ expanded: false }">
                <button @click="expanded = !expanded"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        ভর্তি
                    </span>
                    <svg class="w-4 h-4 transition-transform" :class="expanded && 'rotate-180'" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" x-collapse class="pl-10 space-y-1">
                    <a href="{{ route('admission') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">ভর্তি তথ্য</a>
                    <a href="{{ route('admission.eligibility') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">আবেদনের যোগ্যতা</a>
                    <a href="{{ route('admission.fees') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">ফি স্ট্রাকচার</a>
                    <a href="{{ route('admission.apply') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">অনলাইন আবেদন</a>
                </div>
            </div>

            <!-- Gallery Accordion -->
            <div x-data="{ expanded: false }">
                <button @click="expanded = !expanded"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        গ্যালারি
                    </span>
                    <svg class="w-4 h-4 transition-transform" :class="expanded && 'rotate-180'" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expanded" x-collapse class="pl-10 space-y-1">
                    <a href="{{ route('gallery') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">ফটো গ্যালারি</a>
                    <a href="{{ route('gallery.videos') }}"
                        class="block px-4 py-2 text-gray-600 hover:text-primary-700 rounded-lg">ভিডিও গ্যালারি</a>
                </div>
            </div>

            <a href="{{ route('news') }}"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                সংবাদ
            </a>

            <a href="{{ route('contact') }}"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                যোগাযোগ
            </a>

            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('donate') }}"
                    class="flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-gold-400 to-gold-600 text-white rounded-lg font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    অনুদান দিন
                </a>

                <a href="{{ route('portal') }}"
                    class="flex items-center justify-center gap-2 px-4 py-3 mt-2 border border-primary-600 text-primary-700 rounded-lg font-medium hover:bg-primary-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    পোর্টাল লগইন
                </a>
            </div>
        </nav>
    </div>
</div>