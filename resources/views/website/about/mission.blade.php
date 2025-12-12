@extends('website.layouts.app')

@section('title', 'লক্ষ্য ও উদ্দেশ্য - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">লক্ষ্য ও উদ্দেশ্য</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আমাদের মিশন ও ভিশন</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-primary-50 rounded-2xl p-8" data-aos="fade-right">
                        <h2 class="text-2xl font-bold text-primary-800 mb-4">🎯 আমাদের লক্ষ্য</h2>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-primary-600">✓</span>
                                কুরআন ও সুন্নাহর আলোকে আদর্শ মুসলিম গড়ে তোলা
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-primary-600">✓</span>
                                দ্বীনি ও আধুনিক শিক্ষার সমন্বয় সাধন
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-primary-600">✓</span>
                                চরিত্রবান ও দেশপ্রেমিক নাগরিক তৈরি
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-primary-600">✓</span>
                                হাফেজে কুরআন ও আলেমে দ্বীন তৈরি
                            </li>
                        </ul>
                    </div>
                    <div class="bg-gold-50 rounded-2xl p-8" data-aos="fade-left">
                        <h2 class="text-2xl font-bold text-gold-800 mb-4">🌟 আমাদের ভিশন</h2>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-gold-600">✓</span>
                                বাংলাদেশের সেরা দ্বীনি শিক্ষা প্রতিষ্ঠান হওয়া
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-gold-600">✓</span>
                                আন্তর্জাতিক মানের শিক্ষা প্রদান
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-gold-600">✓</span>
                                সমাজ ও দেশের উন্নয়নে অবদান রাখা
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-gold-600">✓</span>
                                দ্বীন ও দুনিয়ায় সফল মানুষ তৈরি
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection