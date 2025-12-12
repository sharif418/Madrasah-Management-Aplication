@extends('website.layouts.app')

@section('title', 'প্রশ্নোত্তর - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">সাধারণ প্রশ্নোত্তর</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">FAQ</p>
        </div>
    </section>

    <!-- FAQ Content -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                @forelse($faqs as $category => $faqGroup)
                    <div class="mb-8" data-aos="fade-up">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b border-primary-200">{{ $category }}</h2>
                        <div class="space-y-4">
                            @foreach($faqGroup as $faq)
                                <div x-data="{ open: false }" class="bg-gray-50 rounded-xl overflow-hidden">
                                    <button @click="open = !open"
                                        class="flex items-center justify-between w-full px-6 py-4 text-left">
                                        <span class="font-semibold text-gray-900">{{ $faq->question }}</span>
                                        <svg class="w-5 h-5 text-primary-600 transition-transform" :class="open && 'rotate-180'"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-collapse class="px-6 pb-4">
                                        <p class="text-gray-600">{{ $faq->answer }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div class="text-6xl mb-4">❓</div>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">কোন প্রশ্ন পাওয়া যায়নি</h3>
                    </div>
                @endforelse

                <div class="text-center mt-12" data-aos="fade-up">
                    <p class="text-gray-600 mb-4">আরও প্রশ্ন থাকলে যোগাযোগ করুন:</p>
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-primary-600 text-white rounded-full font-semibold hover:bg-primary-700 transition-colors">
                        যোগাযোগ করুন
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection