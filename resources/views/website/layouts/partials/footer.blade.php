<!-- Premium Footer -->
<footer class="bg-gray-900 text-white">
    <!-- Top Wave -->
    <svg class="w-full -mb-1" viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill="currentColor" class="text-gray-50"
            d="M0,96L48,85.3C96,75,192,53,288,58.7C384,64,480,96,576,101.3C672,107,768,85,864,74.7C960,64,1056,64,1152,69.3C1248,75,1344,85,1392,90.7L1440,96L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z" />
    </svg>

    <!-- Main Footer Content -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- About Column -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    @if(institution_logo())
                        <img src="{{ institution_logo() }}" alt="Logo" class="h-12 w-12 rounded-full">
                    @else
                        <div class="h-12 w-12 rounded-full bg-primary-600 flex items-center justify-center text-xl">
                            🕌
                        </div>
                    @endif
                    <h3 class="text-xl font-bold">{{ institution_name() ?? 'মাদরাসা নাম' }}</h3>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">
                    ইসলামী শিক্ষার আলোকিত প্রতিষ্ঠান। কুরআন ও সুন্নাহর আলোকে দ্বীনি ও আধুনিক শিক্ষার সমন্বয়।
                </p>
                <!-- Social Links -->
                <div class="flex gap-3">
                    <a href="#"
                        class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-full flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="w-10 h-10 bg-gray-800 hover:bg-primary-600 rounded-full flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4 text-gold-400">দ্রুত লিংক</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('about') }}"
                            class="text-gray-400 hover:text-white transition-colors">প্রতিষ্ঠান পরিচিতি</a></li>
                    <li><a href="{{ route('about.teachers') }}"
                            class="text-gray-400 hover:text-white transition-colors">শিক্ষক তালিকা</a></li>
                    <li><a href="{{ route('departments') }}"
                            class="text-gray-400 hover:text-white transition-colors">বিভাগসমূহ</a></li>
                    <li><a href="{{ route('admission') }}"
                            class="text-gray-400 hover:text-white transition-colors">ভর্তি তথ্য</a></li>
                    <li><a href="{{ route('gallery') }}"
                            class="text-gray-400 hover:text-white transition-colors">গ্যালারি</a></li>
                    <li><a href="{{ route('downloads') }}"
                            class="text-gray-400 hover:text-white transition-colors">ডাউনলোড</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div>
                <h4 class="text-lg font-semibold mb-4 text-gold-400">রিসোর্স</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('news') }}" class="text-gray-400 hover:text-white transition-colors">সংবাদ</a>
                    </li>
                    <li><a href="{{ route('events') }}"
                            class="text-gray-400 hover:text-white transition-colors">ইভেন্ট</a></li>
                    <li><a href="{{ route('circulars') }}"
                            class="text-gray-400 hover:text-white transition-colors">সার্কুলার</a></li>
                    <li><a href="{{ route('faq') }}"
                            class="text-gray-400 hover:text-white transition-colors">প্রশ্নোত্তর</a></li>
                    <li><a href="{{ route('portal') }}" class="text-gray-400 hover:text-white transition-colors">পোর্টাল
                            লগইন</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="text-lg font-semibold mb-4 text-gold-400">যোগাযোগ</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary-500 mt-1 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-gray-400 text-sm">{{ institution_address() ?? 'ঠিকানা যোগ করুন' }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-gray-400">{{ institution_phone() ?? '০১XXXXXXXXX' }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-gray-400">{{ institution_email() ?? 'info@example.com' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-gray-800">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} {{ institution_name() ?? 'মাদরাসা নাম' }}। সর্বস্বত্ব সংরক্ষিত।
                </p>
                <p class="text-gray-600 text-sm">
                    ডেভেলপড বাই <a href="#" class="text-primary-400 hover:text-primary-300">মাদরাসা ম্যানেজমেন্ট
                        সিস্টেম</a>
                </p>
            </div>
        </div>
    </div>
</footer>