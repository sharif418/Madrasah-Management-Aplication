@extends('website.layouts.app')

@section('title', 'প্রতিষ্ঠান পরিচিতি - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">প্রতিষ্ঠান পরিচিতি</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আমাদের সম্পর্কে জানুন</p>
            <!-- Breadcrumb -->
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li class="text-white">পরিচিতি</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Content -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto prose prose-lg">
                <div data-aos="fade-up">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ institution_name() ?? 'মাদরাসা নাম' }} সম্পর্কে
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        আমাদের প্রতিষ্ঠান কুরআন ও সুন্নাহর আলোকে দ্বীনি ও আধুনিক শিক্ষার সমন্বয়ে একটি আদর্শ শিক্ষা
                        প্রতিষ্ঠান।
                        এখানে ছাত্ররা হিফজুল কুরআন, ইলমে দ্বীন এবং আধুনিক শিক্ষার মাধ্যমে নিজেদের গড়ে তোলার সুযোগ পায়।
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        আমাদের লক্ষ্য হলো এমন আদর্শ মুসলিম তৈরি করা যারা দ্বীন ও দুনিয়া উভয় ক্ষেত্রে সফলতা অর্জন করতে
                        সক্ষম।
                    </p>
                </div>

                <!-- Features Grid -->
                <div class="grid md:grid-cols-2 gap-6 mt-12" data-aos="fade-up">
                    <div class="bg-primary-50 rounded-2xl p-6">
                        <div class="w-12 h-12 bg-primary-600 text-white rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">হিফজ বিভাগ</h3>
                        <p class="text-gray-600 text-sm">কুরআন মাজীদ সম্পূর্ণ মুখস্থ করার সুবর্ণ সুযোগ</p>
                    </div>
                    <div class="bg-gold-50 rounded-2xl p-6">
                        <div class="w-12 h-12 bg-gold-600 text-white rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">অভিজ্ঞ শিক্ষকমণ্ডলী</h3>
                        <p class="text-gray-600 text-sm">দেশের বরেণ্য আলেমদের তত্ত্বাবধানে শিক্ষাদান</p>
                    </div>
                    <div class="bg-blue-50 rounded-2xl p-6">
                        <div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">আধুনিক সুযোগ-সুবিধা</h3>
                        <p class="text-gray-600 text-sm">শীতাতপ নিয়ন্ত্রিত শ্রেণীকক্ষ ও আবাসিক ব্যবস্থা</p>
                    </div>
                    <div class="bg-purple-50 rounded-2xl p-6">
                        <div class="w-12 h-12 bg-purple-600 text-white rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">সরকারি স্বীকৃতি</h3>
                        <p class="text-gray-600 text-sm">বাংলাদেশ মাদরাসা শিক্ষা বোর্ড অনুমোদিত</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('about.history') }}"
                    class="px-6 py-3 bg-white rounded-full shadow hover:shadow-lg transition-shadow font-medium text-gray-700 hover:text-primary-600">
                    ইতিহাস
                </a>
                <a href="{{ route('about.mission') }}"
                    class="px-6 py-3 bg-white rounded-full shadow hover:shadow-lg transition-shadow font-medium text-gray-700 hover:text-primary-600">
                    লক্ষ্য ও উদ্দেশ্য
                </a>
                <a href="{{ route('about.committee') }}"
                    class="px-6 py-3 bg-white rounded-full shadow hover:shadow-lg transition-shadow font-medium text-gray-700 hover:text-primary-600">
                    পরিচালনা কমিটি
                </a>
                <a href="{{ route('about.teachers') }}"
                    class="px-6 py-3 bg-white rounded-full shadow hover:shadow-lg transition-shadow font-medium text-gray-700 hover:text-primary-600">
                    শিক্ষক তালিকা
                </a>
            </div>
        </div>
    </section>
@endsection