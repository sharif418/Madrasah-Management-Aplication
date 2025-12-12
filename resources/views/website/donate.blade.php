@extends('website.layouts.app')

@section('title', 'অনুদান দিন - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">অনুদান দিন</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">দ্বীনি শিক্ষার প্রসারে সহযোগিতা করুন
            </p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li class="text-white">অনুদান</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Donation Content -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Quote -->
                <div class="text-center mb-12" data-aos="fade-up">
                    <p class="text-2xl text-primary-700 font-arabic mb-4">
                        "مَنْ دَلَّ عَلَى خَيْرٍ فَلَهُ مِثْلُ أَجْرِ فَاعِلِهِ"
                    </p>
                    <p class="text-gray-600">
                        "যে ব্যক্তি কল্যাণের দিকে পথ দেখায়, তার জন্য সেই কাজ করার সমান সওয়াব রয়েছে।" - সহীহ মুসলিম
                    </p>
                </div>

                <!-- Donation Info -->
                <div class="bg-gradient-to-br from-primary-50 to-gold-50 rounded-3xl p-8 mb-12" data-aos="fade-up">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 text-center">কেন অনুদান দেবেন?</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-3xl">📚</span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-1">দ্বীনি শিক্ষা</h4>
                            <p class="text-sm text-gray-600">গরীব ছাত্রদের বিনামূল্যে কুরআন শিক্ষা</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gold-100 rounded-full flex items-center justify-center">
                                <span class="text-3xl">🏫</span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-1">অবকাঠামো উন্নয়ন</h4>
                            <p class="text-sm text-gray-600">আধুনিক শ্রেণীকক্ষ ও সুযোগ-সুবিধা</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-3xl">🍽️</span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-1">আহার ব্যবস্থা</h4>
                            <p class="text-sm text-gray-600">আবাসিক ছাত্রদের খাবার সরবরাহ</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- bKash -->
                    <div class="bg-[#E2136E]/5 border-2 border-[#E2136E]/20 rounded-2xl p-6 text-center"
                        data-aos="fade-right">
                        <div class="w-20 h-20 mx-auto mb-4 bg-[#E2136E] rounded-2xl flex items-center justify-center">
                            <span class="text-white text-3xl font-bold">bKash</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">বিকাশ</h3>
                        <p class="text-3xl font-bold text-[#E2136E] mb-4">০১XXXXXXXXX</p>
                        <p class="text-gray-600 text-sm">
                            Send Money অপশন ব্যবহার করে পাঠান
                        </p>
                    </div>

                    <!-- Nagad -->
                    <div class="bg-[#F6921E]/5 border-2 border-[#F6921E]/20 rounded-2xl p-6 text-center"
                        data-aos="fade-left">
                        <div class="w-20 h-20 mx-auto mb-4 bg-[#F6921E] rounded-2xl flex items-center justify-center">
                            <span class="text-white text-3xl font-bold">নগদ</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">নগদ</h3>
                        <p class="text-3xl font-bold text-[#F6921E] mb-4">০১XXXXXXXXX</p>
                        <p class="text-gray-600 text-sm">
                            Send Money অপশন ব্যবহার করে পাঠান
                        </p>
                    </div>
                </div>

                <!-- Bank Account -->
                <div class="mt-8 bg-gray-50 rounded-2xl p-6" data-aos="fade-up">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-center">ব্যাংক একাউন্ট</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">ব্যাংকের নাম:</span>
                            <span class="font-semibold">ইসলামী ব্যাংক বাংলাদেশ লিমিটেড</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">শাখা:</span>
                            <span class="font-semibold">মতিঝিল শাখা</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">একাউন্ট নাম:</span>
                            <span class="font-semibold">{{ institution_name() ?? 'মাদরাসা নাম' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">একাউন্ট নং:</span>
                            <span class="font-semibold">XXXXXXXXXXXX</span>
                        </div>
                    </div>
                </div>

                <!-- Contact for Donation -->
                <div class="mt-8 text-center" data-aos="fade-up">
                    <p class="text-gray-600 mb-4">অনুদান সংক্রান্ত যেকোনো তথ্যের জন্য যোগাযোগ করুন:</p>
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-primary-600 text-white rounded-full font-semibold hover:bg-primary-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        যোগাযোগ করুন
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection