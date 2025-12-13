@extends('website.layouts.app')

@section('title', 'সিস্টেম ব্যবহার নির্দেশিকা - মাদরাসা ম্যানেজমেন্ট সিস্টেম')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 text-white py-20">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur rounded-full px-4 py-2 mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-sm">সম্পূর্ণ ইউজার গাইড</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">মাদরাসা ম্যানেজমেন্ট সিস্টেম</h1>
                <p class="text-xl text-white/80 mb-8">
                    এই গাইড পড়ে যেকোনো মানুষ পুরো সিস্টেম চালাতে সক্ষম হবে
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#get-started"
                        class="bg-white text-primary-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        শুরু করুন
                    </a>
                    <a href="#all-modules"
                        class="border-2 border-white/30 text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/10 transition">
                        সব মডিউল দেখুন
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Navigation -->
    <section class="bg-white py-8 border-b sticky top-0 z-40 shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap justify-center gap-4 text-sm">
                <a href="#get-started" class="px-4 py-2 bg-gray-100 hover:bg-primary-100 rounded-full transition">🚀
                    শুরু</a>
                <a href="#login-guide" class="px-4 py-2 bg-gray-100 hover:bg-primary-100 rounded-full transition">🔐
                    লগইন</a>
                <a href="#dashboard" class="px-4 py-2 bg-gray-100 hover:bg-primary-100 rounded-full transition">📊
                    ড্যাশবোর্ড</a>
                <a href="#student-management"
                    class="px-4 py-2 bg-gray-100 hover:bg-primary-100 rounded-full transition">👨‍🎓 ছাত্র</a>
                <a href="#teacher-management"
                    class="px-4 py-2 bg-gray-100 hover:bg-primary-100 rounded-full transition">👨‍🏫 শিক্ষক</a>
                <a href="#attendance" class="px-4 py-2 bg-gray-100 hover:bg-primary-100 rounded-full transition">📋
                    হাজিরা</a>
                <a href="#fee-management" class="px-4 py-2 bg-gray-100 hover:bg-primary-100 rounded-full transition">💰
                    ফি</a>
                <a href="#exam" class="px-4 py-2 bg-gray-100 hover:bg-primary-100 rounded-full transition">📝 পরীক্ষা</a>
                <a href="#roles" class="px-4 py-2 bg-gray-100 hover:bg-primary-100 rounded-full transition">👥 রোল</a>
            </div>
        </div>
    </section>

    <!-- Get Started Section -->
    <section id="get-started" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">🚀 কিভাবে শুরু করবেন</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">সিস্টেম ব্যবহার শুরু করতে এই ৩টি সহজ ধাপ অনুসরণ করুন</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Step 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border-t-4 border-primary-500 relative">
                    <div
                        class="absolute -top-4 left-8 bg-primary-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">
                        ১</div>
                    <div class="text-4xl mb-4">🔗</div>
                    <h3 class="text-xl font-bold mb-3">ওয়েবসাইটে যান</h3>
                    <p class="text-gray-600 mb-4">ব্রাউজারে আপনার মাদরাসার ওয়েবসাইট URL লিখুন</p>
                    <div class="bg-gray-100 rounded-lg p-3 font-mono text-sm">
                        https://darulabrar.online
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border-t-4 border-gold-500 relative">
                    <div
                        class="absolute -top-4 left-8 bg-gold-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">
                        ২</div>
                    <div class="text-4xl mb-4">🔐</div>
                    <h3 class="text-xl font-bold mb-3">পোর্টালে লগইন</h3>
                    <p class="text-gray-600 mb-4">আপনার রোল অনুযায়ী সঠিক পোর্টালে যান</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2 bg-blue-50 p-2 rounded">
                            <span class="font-semibold">Admin:</span>
                            <code>/admin</code>
                        </div>
                        <div class="flex items-center gap-2 bg-green-50 p-2 rounded">
                            <span class="font-semibold">Student:</span>
                            <code>/student</code>
                        </div>
                        <div class="flex items-center gap-2 bg-purple-50 p-2 rounded">
                            <span class="font-semibold">Parent:</span>
                            <code>/parent</code>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border-t-4 border-green-500 relative">
                    <div
                        class="absolute -top-4 left-8 bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">
                        ৩</div>
                    <div class="text-4xl mb-4">✅</div>
                    <h3 class="text-xl font-bold mb-3">কাজ শুরু করুন</h3>
                    <p class="text-gray-600 mb-4">ড্যাশবোর্ডে প্রবেশ করে আপনার কাজ শুরু করুন</p>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-700">
                        ✨ সবকিছু বাংলায় লেখা আছে!
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Guide Section -->
    <section id="login-guide" class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">🔐 লগইন করার নিয়ম</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">প্রতিটি ব্যবহারকারীর জন্য আলাদা লগইন পোর্টাল</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Admin Login -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl overflow-hidden shadow-xl text-white">
                    <div class="p-8">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl mb-6">👨‍💼
                        </div>
                        <h3 class="text-2xl font-bold mb-3">অ্যাডমিন প্যানেল</h3>
                        <p class="text-white/80 mb-6">শিক্ষক, স্টাফ ও প্রশাসনিক কর্মীদের জন্য</p>
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4 mb-6">
                            <div class="font-mono text-lg mb-2">/admin</div>
                            <div class="text-sm text-white/70">https://darulabrar.online/admin</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                সুপার অ্যাডমিন
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                প্রধান শিক্ষক
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                শিক্ষক ও স্টাফ
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Login -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl overflow-hidden shadow-xl text-white">
                    <div class="p-8">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl mb-6">👨‍🎓
                        </div>
                        <h3 class="text-2xl font-bold mb-3">ছাত্র পোর্টাল</h3>
                        <p class="text-white/80 mb-6">ছাত্রদের নিজস্ব ড্যাশবোর্ড</p>
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4 mb-6">
                            <div class="font-mono text-lg mb-2">/student</div>
                            <div class="text-sm text-white/70">https://darulabrar.online/student</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                নিজের প্রোফাইল দেখা
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                রেজাল্ট দেখা
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                হাজিরা ও ফি দেখা
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent Login -->
                <div
                    class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl overflow-hidden shadow-xl text-white">
                    <div class="p-8">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl mb-6">
                            👨‍👩‍👧</div>
                        <h3 class="text-2xl font-bold mb-3">অভিভাবক পোর্টাল</h3>
                        <p class="text-white/80 mb-6">অভিভাবকদের তথ্য দেখার সুযোগ</p>
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4 mb-6">
                            <div class="font-mono text-lg mb-2">/parent</div>
                            <div class="text-sm text-white/70">https://darulabrar.online/parent</div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                সন্তানের তথ্য দেখা
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                ফি এর হিসাব দেখা
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                নোটিস পড়া
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Overview -->
    <section id="dashboard" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">📊 ড্যাশবোর্ড পরিচিতি</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">অ্যাডমিন প্যানেলে প্রবেশ করলে যা দেখতে পাবেন</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-5xl mx-auto">
                <div class="bg-gray-800 p-4 flex items-center gap-2">
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <div class="flex-1 text-center text-gray-400 text-sm">মাদরাসা ম্যানেজমেন্ট - অ্যাডমিন ড্যাশবোর্ড</div>
                </div>
                <div class="p-8">
                    <div class="grid md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
                            <div class="text-3xl font-bold text-blue-600">১২০</div>
                            <div class="text-sm text-gray-600">মোট ছাত্র</div>
                        </div>
                        <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-center">
                            <div class="text-3xl font-bold text-green-600">১৫</div>
                            <div class="text-sm text-gray-600">মোট শিক্ষক</div>
                        </div>
                        <div class="bg-purple-50 border border-purple-100 rounded-xl p-4 text-center">
                            <div class="text-3xl font-bold text-purple-600">৮৫%</div>
                            <div class="text-sm text-gray-600">উপস্থিতি</div>
                        </div>
                        <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 text-center">
                            <div class="text-3xl font-bold text-orange-600">৫২,০০০</div>
                            <div class="text-sm text-gray-600">ফি আদায়</div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="border rounded-xl p-4">
                            <h4 class="font-semibold mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                সাম্প্রতিক হাজিরা
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between p-2 bg-gray-50 rounded">
                                    <span>ক্লাস ১ - আজকে</span>
                                    <span class="text-green-600">✓ সম্পন্ন</span>
                                </div>
                                <div class="flex justify-between p-2 bg-gray-50 rounded">
                                    <span>ক্লাস ২ - আজকে</span>
                                    <span class="text-yellow-600">⏳ বাকি</span>
                                </div>
                            </div>
                        </div>
                        <div class="border rounded-xl p-4">
                            <h4 class="font-semibold mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                সাম্প্রতিক ভর্তি
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between p-2 bg-gray-50 rounded">
                                    <span>মোঃ আব্দুল্লাহ</span>
                                    <span class="text-gray-500">ক্লাস ১</span>
                                </div>
                                <div class="flex justify-between p-2 bg-gray-50 rounded">
                                    <span>মোঃ ইব্রাহীম</span>
                                    <span class="text-gray-500">ক্লাস ২</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- All Modules Section -->
    <section id="all-modules" class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">📦 সকল মডিউল</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">সিস্টেমে যা যা করতে পারবেন</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                @php
                    $modules = [
                        ['icon' => '👨‍🎓', 'name' => 'ছাত্র ব্যবস্থাপনা', 'features' => ['নতুন ছাত্র ভর্তি', 'ছাত্র তথ্য সম্পাদনা', 'আইডি কার্ড প্রিন্ট', 'TC জেনারেট'], 'color' => 'blue'],
                        ['icon' => '👨‍🏫', 'name' => 'শিক্ষক ব্যবস্থাপনা', 'features' => ['শিক্ষক যোগ', 'প্রোফাইল আপডেট', 'আইডি কার্ড', 'ছুটির আবেদন'], 'color' => 'green'],
                        ['icon' => '📋', 'name' => 'হাজিরা', 'features' => ['দৈনিক হাজিরা', 'বাল্ক হাজিরা', 'হাজিরা রিপোর্ট', 'SMS অ্যালার্ট'], 'color' => 'yellow'],
                        ['icon' => '📝', 'name' => 'পরীক্ষা', 'features' => ['পরীক্ষা তৈরি', 'মার্কস এন্ট্রি', 'রেজাল্ট প্রকাশ', 'মার্কশিট প্রিন্ট'], 'color' => 'purple'],
                        ['icon' => '💰', 'name' => 'ফি ব্যবস্থাপনা', 'features' => ['ফি আদায়', 'রসিদ প্রিন্ট', 'বকেয়া তালিকা', 'ফি রিপোর্ট'], 'color' => 'pink'],
                        ['icon' => '🏦', 'name' => 'হিসাব', 'features' => ['আয়-ব্যয়', 'বেতন প্রদান', 'ব্যাংক একাউন্ট', 'বাজেট'], 'color' => 'indigo'],
                        ['icon' => '📚', 'name' => 'লাইব্রেরি', 'features' => ['বই যোগ', 'বই ইস্যু', 'রিটার্ন', 'জরিমানা'], 'color' => 'teal'],
                        ['icon' => '🏠', 'name' => 'হোস্টেল', 'features' => ['রুম ব্যবস্থাপনা', 'সিট বরাদ্দ', 'খাবার মেনু', 'ভিজিটর লগ'], 'color' => 'orange'],
                        ['icon' => '🚌', 'name' => 'পরিবহন', 'features' => ['গাড়ি তালিকা', 'রুট', 'বরাদ্দ', 'মেইনটেন্যান্স'], 'color' => 'cyan'],
                        ['icon' => '🕌', 'name' => 'হিফজ ও কিতাব', 'features' => ['হিফজ প্রগ্রেস', 'কিতাব প্রগ্রেস', 'সাবক/সবুত', 'সামারি'], 'color' => 'emerald'],
                        ['icon' => '📢', 'name' => 'যোগাযোগ', 'features' => ['নোটিস', 'সার্কুলার', 'ইভেন্ট', 'SMS'], 'color' => 'rose'],
                        ['icon' => '🌐', 'name' => 'ওয়েবসাইট', 'features' => ['স্লাইডার', 'সংবাদ', 'গ্যালারি', 'ডাউনলোড'], 'color' => 'sky'],
                    ];
                @endphp

                @foreach($modules as $module)
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow p-6 border-l-4 border-{{ $module['color'] }}-500">
                        <div class="text-3xl mb-3">{{ $module['icon'] }}</div>
                        <h3 class="font-bold mb-3">{{ $module['name'] }}</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            @foreach($module['features'] as $feature)
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Student Management Guide -->
    <section id="student-management" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">👨‍🎓 ছাত্র ব্যবস্থাপনা</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">নতুন ছাত্র ভর্তি থেকে শুরু করে সবকিছু</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <!-- Step by Step -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-blue-600 text-white p-4">
                        <h3 class="text-xl font-bold">📝 নতুন ছাত্র ভর্তি করার ধাপ</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600">
                                    ১</div>
                                <div>
                                    <h4 class="font-semibold mb-1">মেনু থেকে "ছাত্র ব্যবস্থাপনা" → "ছাত্র" ক্লিক করুন</h4>
                                    <p class="text-gray-600 text-sm">বাম পাশের মেনু থেকে খুঁজে নিন</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600">
                                    ২</div>
                                <div>
                                    <h4 class="font-semibold mb-1">"নতুন ছাত্র যোগ করুন" বাটনে ক্লিক করুন</h4>
                                    <p class="text-gray-600 text-sm">পেজের উপরে ডান কোনায় বাটন পাবেন</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600">
                                    ৩</div>
                                <div>
                                    <h4 class="font-semibold mb-1">ফর্ম পূরণ করুন</h4>
                                    <p class="text-gray-600 text-sm">নাম, পিতার নাম, ক্লাস, ইত্যাদি সব তথ্য দিন</p>
                                    <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded p-3 text-sm">
                                        💡 <strong>টিপস:</strong> Email দিলে auto login তৈরি হবে!
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center font-bold text-green-600">
                                    ৪</div>
                                <div>
                                    <h4 class="font-semibold mb-1">"সেভ করুন" বাটনে ক্লিক করুন</h4>
                                    <p class="text-gray-600 text-sm">ভর্তি নম্বর স্বয়ংক্রিয়ভাবে তৈরি হবে!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Attendance Guide -->
    <section id="attendance" class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">📋 হাজিরা নেওয়ার নিয়ম</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">দৈনিক উপস্থিতি রেকর্ড করুন</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">📋</div>
                        <h3 class="text-xl font-bold">দৈনিক হাজিরা</h3>
                    </div>
                    <ol class="space-y-3 text-gray-600">
                        <li class="flex gap-3">
                            <span class="font-bold text-green-600">১.</span>
                            "উপস্থিতি ব্যবস্থাপনা" মেনুতে যান
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-green-600">২.</span>
                            "ছাত্র হাজিরা" ক্লিক করুন
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-green-600">৩.</span>
                            তারিখ ও ক্লাস সিলেক্ট করুন
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-green-600">৪.</span>
                            প্রতি ছাত্রের পাশে উপস্থিত/অনুপস্থিত মার্ক করুন
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-green-600">৫.</span>
                            "সেভ করুন" বাটনে ক্লিক করুন
                        </li>
                    </ol>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">⚡</div>
                        <h3 class="text-xl font-bold">বাল্ক হাজিরা (দ্রুত)</h3>
                    </div>
                    <ol class="space-y-3 text-gray-600">
                        <li class="flex gap-3">
                            <span class="font-bold text-blue-600">১.</span>
                            "বাল্ক হাজিরা" বাটনে ক্লিক করুন
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-blue-600">২.</span>
                            ক্লাস সিলেক্ট করুন
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-blue-600">৩.</span>
                            "সবাই লোড করুন" বাটনে ক্লিক করুন
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-blue-600">৪.</span>
                            শুধু অনুপস্থিতদের পরিবর্তন করুন
                        </li>
                        <li class="flex gap-3">
                            <span class="font-bold text-blue-600">৫.</span>
                            একসাথে সব সেভ হয়ে যাবে!
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Fee Management -->
    <section id="fee-management" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">💰 ফি আদায়</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">ছাত্রদের ফি নেওয়া ও রসিদ প্রদান</p>
            </div>

            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-pink-500 to-rose-500 text-white p-4">
                    <h3 class="text-xl font-bold">💳 ফি আদায়ের ধাপ</h3>
                </div>
                <div class="p-6">
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                                ১</div>
                            <h4 class="font-semibold mb-2">ছাত্র খুঁজুন</h4>
                            <p class="text-sm text-gray-600">ফি ব্যবস্থাপনা → ফি আদায় → ছাত্রের নাম বা ভর্তি নম্বর দিয়ে
                                সার্চ করুন</p>
                        </div>
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                                ২</div>
                            <h4 class="font-semibold mb-2">পেমেন্ট নিন</h4>
                            <p class="text-sm text-gray-600">"পেমেন্ট নিন" বাটনে ক্লিক করে পরিমাণ ও পদ্ধতি দিন</p>
                        </div>
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                                ✓</div>
                            <h4 class="font-semibold mb-2">রসিদ প্রিন্ট</h4>
                            <p class="text-sm text-gray-600">স্বয়ংক্রিয়ভাবে রসিদ তৈরি হবে, প্রিন্ট করে দিন</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Exam Management -->
    <section id="exam" class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">📝 পরীক্ষা ব্যবস্থাপনা</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">পরীক্ষা তৈরি থেকে রেজাল্ট প্রকাশ পর্যন্ত</p>
            </div>

            <div class="max-w-5xl mx-auto">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
                        <div class="text-2xl mb-2">📅</div>
                        <h4 class="font-bold mb-2">ধাপ ১: পরীক্ষা তৈরি</h4>
                        <p class="text-sm text-gray-600">পরীক্ষা ব্যবস্থাপনা → পরীক্ষা → নতুন পরীক্ষা যোগ করুন</p>
                    </div>
                    <div class="hidden md:flex items-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                    <div class="flex-1 bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                        <div class="text-2xl mb-2">✏️</div>
                        <h4 class="font-bold mb-2">ধাপ ২: মার্কস এন্ট্রি</h4>
                        <p class="text-sm text-gray-600">পরীক্ষায় → মার্কস এন্ট্রি → বিষয়ভিত্তিক নম্বর দিন</p>
                    </div>
                    <div class="hidden md:flex items-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                    <div class="flex-1 bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                        <div class="text-2xl mb-2">📊</div>
                        <h4 class="font-bold mb-2">ধাপ ৩: রেজাল্ট</h4>
                        <p class="text-sm text-gray-600">ট্যাবুলেশন শীট ও মার্কশিট প্রিন্ট করুন</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles Section -->
    <section id="roles" class="py-16 bg-gradient-to-br from-gray-800 to-gray-900 text-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">👥 ইউজার রোল ও পারমিশন</h2>
                <p class="text-gray-400 max-w-2xl mx-auto">প্রতিটি রোলের কাজ ও অধিকার</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
                @php
                    $roles = [
                        ['name' => 'সুপার অ্যাডমিন', 'icon' => '👑', 'desc' => 'সব কিছু করার ক্ষমতা', 'color' => 'yellow'],
                        ['name' => 'প্রধান শিক্ষক', 'icon' => '🎓', 'desc' => 'সব দেখতে পারবে + অনুমোদন', 'color' => 'blue'],
                        ['name' => 'একাডেমিক অ্যাডমিন', 'icon' => '📚', 'desc' => 'ছাত্র, পরীক্ষা, হাজিরা', 'color' => 'green'],
                        ['name' => 'হিসাব অ্যাডমিন', 'icon' => '💰', 'desc' => 'ফি, আয়, ব্যয়, বেতন', 'color' => 'pink'],
                        ['name' => 'শিক্ষক', 'icon' => '👨‍🏫', 'desc' => 'হাজিরা, মার্কস, হিফজ', 'color' => 'purple'],
                        ['name' => 'লাইব্রেরিয়ান', 'icon' => '📖', 'desc' => 'শুধু লাইব্রেরি', 'color' => 'teal'],
                        ['name' => 'হোস্টেল তত্ত্বাবধায়ক', 'icon' => '🏠', 'desc' => 'শুধু হোস্টেল', 'color' => 'orange'],
                        ['name' => 'ছাত্র', 'icon' => '👨‍🎓', 'desc' => 'নিজের তথ্য দেখা', 'color' => 'cyan'],
                        ['name' => 'অভিভাবক', 'icon' => '👨‍👩‍👧', 'desc' => 'সন্তানের তথ্য দেখা', 'color' => 'rose'],
                    ];
                @endphp

                @foreach($roles as $role)
                    <div class="bg-white/10 backdrop-blur rounded-xl p-5 hover:bg-white/20 transition">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">{{ $role['icon'] }}</span>
                            <span class="font-bold">{{ $role['name'] }}</span>
                        </div>
                        <p class="text-sm text-gray-400">{{ $role['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">❓ সাধারণ প্রশ্নোত্তর</h2>
            </div>

            <div class="max-w-3xl mx-auto space-y-4">
                <div class="bg-white rounded-xl shadow border p-6" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left">
                        <span class="font-semibold">পাসওয়ার্ড ভুলে গেলে কি করব?</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 text-gray-600">
                        লগইন পেজে "Forgot Password" লিংকে ক্লিক করুন অথবা অ্যাডমিনের সাথে যোগাযোগ করুন।
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border p-6" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left">
                        <span class="font-semibold">ফি রসিদ প্রিন্ট হচ্ছে না?</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 text-gray-600">
                        ব্রাউজারের Pop-up Block বন্ধ করুন। Chrome এ Address bar এর পাশে Pop-up blocked দেখলে Allow করুন।
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border p-6" x-data="{ open: false }">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left">
                        <span class="font-semibold">একই ছাত্র দুইবার ভর্তি হয়ে গেছে?</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 text-gray-600">
                        ছাত্র তালিকায় গিয়ে ডুপ্লিকেট এন্ট্রি মুছে ফেলুন। মুছে ফেলার আগে কোনটি সঠিক তা যাচাই করুন।
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Developer Info -->
    <section class="py-12 bg-primary-900 text-white">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-2xl mx-auto">
                <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">💻
                </div>
                <h3 class="text-2xl font-bold mb-3">ডেভেলপড বাই</h3>
                <p class="text-3xl font-bold text-gold-400 mb-2">Sharif Mohammad Nasrullah</p>
                <p class="text-gray-400 mb-6">Full Stack Developer</p>
                <div class="flex justify-center gap-4">
                    <a href="mailto:contact@example.com"
                        class="bg-white/10 hover:bg-white/20 px-6 py-3 rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        যোগাযোগ
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection