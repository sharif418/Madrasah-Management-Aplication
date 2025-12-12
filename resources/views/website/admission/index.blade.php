@extends('website.layouts.app')

@section('title', 'ভর্তি তথ্য - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section class="pt-32 pb-20" style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">ভর্তি তথ্য</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আপনার সন্তানকে আদর্শ মানুষ হিসেবে
                গড়ে তুলুন</p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li class="text-white">ভর্তি</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Admission Content -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <!-- CTA Banner -->
                <div class="bg-gradient-to-r from-gold-400 to-gold-600 rounded-3xl p-8 mb-12 text-center text-gray-900"
                    data-aos="fade-up">
                    <h2 class="text-2xl md:text-3xl font-bold mb-2">ভর্তি চলছে!</h2>
                    <p class="text-lg opacity-90 mb-4">নতুন শিক্ষাবর্ষের জন্য ভর্তি আবেদন গ্রহণ করা হচ্ছে</p>
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gray-900 text-white rounded-full font-semibold hover:bg-gray-800 transition-colors">
                        যোগাযোগ করুন
                    </a>
                </div>

                <!-- Departments -->
                <div class="mb-12" data-aos="fade-up">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">বিভাগসমূহ</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-primary-50 rounded-2xl p-6">
                            <div
                                class="w-12 h-12 bg-primary-600 text-white rounded-xl flex items-center justify-center mb-4">
                                <span class="text-xl">📖</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">হিফজ বিভাগ</h3>
                            <p class="text-gray-600 text-sm">কুরআন মাজীদ সম্পূর্ণ মুখস্থ করার বিশেষ বিভাগ। অভিজ্ঞ হাফেজদের
                                তত্ত্বাবধানে।</p>
                        </div>
                        <div class="bg-gold-50 rounded-2xl p-6">
                            <div class="w-12 h-12 bg-gold-600 text-white rounded-xl flex items-center justify-center mb-4">
                                <span class="text-xl">📚</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">কিতাব বিভাগ</h3>
                            <p class="text-gray-600 text-sm">ইলমে দ্বীন অর্জনের জন্য বিভিন্ন কিতাব পাঠ্যক্রম। আলিম, ফাযিল
                                স্তর।</p>
                        </div>
                        <div class="bg-blue-50 rounded-2xl p-6">
                            <div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center mb-4">
                                <span class="text-xl">🎓</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">নাজেরা বিভাগ</h3>
                            <p class="text-gray-600 text-sm">কুরআন শরীফ সহীহভাবে পড়া শেখার প্রাথমিক বিভাগ।</p>
                        </div>
                        <div class="bg-purple-50 rounded-2xl p-6">
                            <div
                                class="w-12 h-12 bg-purple-600 text-white rounded-xl flex items-center justify-center mb-4">
                                <span class="text-xl">🌟</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">তাখাসসুস বিভাগ</h3>
                            <p class="text-gray-600 text-sm">উচ্চতর দ্বীনি শিক্ষা ও বিশেষ বিষয়ে দক্ষতা অর্জন।</p>
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="mb-12" data-aos="fade-up">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">ভর্তির যোগ্যতা</h2>
                    <div class="bg-gray-50 rounded-2xl p-6">
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">ছাত্রের বয়স ৫-১৫ বছরের মধ্যে হতে হবে (বিভাগ অনুযায়ী)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">জন্ম নিবন্ধন সনদের কপি</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">পাসপোর্ট সাইজ ছবি (২ কপি)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">পূর্ববর্তী প্রতিষ্ঠানের ছাড়পত্র (প্রযোজ্য ক্ষেত্রে)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-gray-700">অভিভাবকের NID কপি</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Contact for Admission -->
                <div class="text-center" data-aos="fade-up">
                    <p class="text-gray-600 mb-4">ভর্তি সংক্রান্ত যেকোনো তথ্যের জন্য যোগাযোগ করুন:</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="tel:{{ institution_phone() }}"
                            class="inline-flex items-center gap-2 px-8 py-3 bg-primary-600 text-white rounded-full font-semibold hover:bg-primary-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            কল করুন
                        </a>
                        <a href="{{ route('contact') }}"
                            class="inline-flex items-center gap-2 px-8 py-3 border-2 border-primary-600 text-primary-600 rounded-full font-semibold hover:bg-primary-50 transition-colors">
                            বার্তা পাঠান
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection