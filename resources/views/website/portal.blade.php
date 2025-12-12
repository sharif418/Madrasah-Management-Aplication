@extends('website.layouts.app')

@section('title', 'পোর্টাল - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">পোর্টাল লগইন</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আপনার একাউন্টে প্রবেশ করুন</p>
        </div>
    </section>

    <!-- Portal Links -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Admin Portal -->
                    <a href="/admin" class="group" data-aos="fade-up" data-aos-delay="0">
                        <div
                            class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-3xl p-8 text-center text-white hover:-translate-y-2 transition-transform">
                            <div class="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">অ্যাডমিন প্যানেল</h3>
                            <p class="text-primary-100 text-sm">প্রশাসনিক কার্যক্রম</p>
                        </div>
                    </a>

                    <!-- Teacher Portal -->
                    <a href="/admin" class="group" data-aos="fade-up" data-aos-delay="100">
                        <div
                            class="bg-gradient-to-br from-gold-400 to-gold-600 rounded-3xl p-8 text-center text-gray-900 hover:-translate-y-2 transition-transform">
                            <div class="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">শিক্ষক পোর্টাল</h3>
                            <p class="text-gray-700 text-sm">হাজিরা ও ফলাফল</p>
                        </div>
                    </a>

                    <!-- Student/Parent Portal -->
                    <a href="#" class="group" data-aos="fade-up" data-aos-delay="200">
                        <div
                            class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-3xl p-8 text-center text-white hover:-translate-y-2 transition-transform">
                            <div class="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">অভিভাবক পোর্টাল</h3>
                            <p class="text-blue-100 text-sm">ফলাফল ও অগ্রগতি</p>
                            <span class="inline-block mt-2 px-3 py-1 bg-white/20 rounded-full text-xs">শীঘ্রই আসছে</span>
                        </div>
                    </a>
                </div>

                <!-- Help Text -->
                <div class="text-center mt-12" data-aos="fade-up">
                    <p class="text-gray-600">
                        লগইন সমস্যা হলে যোগাযোগ করুন:
                        <a href="{{ route('contact') }}" class="text-primary-600 font-semibold hover:underline">সাপোর্ট</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection