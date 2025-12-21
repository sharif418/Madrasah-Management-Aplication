@extends('website.layouts.app')

@section('title', 'ভর্তির যোগ্যতা - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section class="pt-32 pb-20" style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">
                {{ setting('eligibility_title', 'ভর্তির যোগ্যতা') }}
            </h1>
            <p class="text-xl opacity-80" data-aos="fade-up" data-aos-delay="100">বিভাগ অনুযায়ী প্রয়োজনীয় যোগ্যতা</p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li><a href="{{ route('admission') }}" class="hover:text-white">ভর্তি</a></li>
                    <li>/</li>
                    <li class="text-white">যোগ্যতা</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Eligibility Content -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">

                @if(setting('eligibility_content'))
                    <!-- Dynamic Content from Admin -->
                    <div class="prose prose-lg max-w-none" data-aos="fade-up">
                        {!! setting('eligibility_content') !!}
                    </div>
                @else
                    <!-- Default Content (shown when no content set in admin) -->

                    <!-- General Requirements -->
                    <div class="bg-primary-50 rounded-3xl p-8 mb-12" data-aos="fade-up">
                        <h2 class="text-2xl font-bold text-primary-800 mb-6 flex items-center gap-3">
                            <span
                                class="w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center">📋</span>
                            সাধারণ প্রয়োজনীয়তা
                        </h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            @php
                                $generalReqs = [
                                    'জন্ম নিবন্ধন সনদের সত্যায়িত কপি',
                                    'পাসপোর্ট সাইজ ছবি (৪ কপি)',
                                    'অভিভাবকের NID কপি',
                                    'পূর্ববর্তী প্রতিষ্ঠানের ছাড়পত্র (প্রযোজ্য ক্ষেত্রে)',
                                    'সর্বশেষ পরীক্ষার ফলাফলের কপি',
                                    'স্বাস্থ্য সনদ (প্রযোজ্য ক্ষেত্রে)',
                                ];
                            @endphp
                            @foreach($generalReqs as $req)
                                <div class="flex items-start gap-3 bg-white rounded-xl p-4">
                                    <div
                                        class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center flex-shrink-0">
                                        ✓</div>
                                    <span>{{ $req }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Department-wise Eligibility -->
                    <div class="space-y-8" data-aos="fade-up">
                        @php
                            $departments = [
                                ['name' => 'নাজেরা বিভাগ', 'icon' => '📖', 'color' => 'blue', 'age' => '৫-১০ বছর', 'requirements' => ['ছাত্রের বয়স ৫-১০ বছরের মধ্যে হতে হবে', 'কোনো পূর্ব শিক্ষাগত যোগ্যতা প্রয়োজন নেই', 'প্রাথমিক বাংলা পড়ার ক্ষমতা থাকলে ভালো']],
                                ['name' => 'হিফজ বিভাগ', 'icon' => '🕌', 'color' => 'green', 'age' => '৭-১২ বছর', 'requirements' => ['ছাত্রের বয়স ৭-১২ বছরের মধ্যে হতে হবে', 'নাজেরা সম্পন্ন বা আমপারা মুখস্থ থাকতে হবে', 'মেধা ও স্মৃতিশক্তি পরীক্ষায় উত্তীর্ণ হতে হবে']],
                                ['name' => 'কিতাব বিভাগ', 'icon' => '📚', 'color' => 'purple', 'age' => '১০-১৫ বছর', 'requirements' => ['ছাত্রের বয়স ১০-১৫ বছরের মধ্যে হতে হবে', 'প্রাথমিক শিক্ষা সম্পন্ন (৫ম শ্রেণী পাস)', 'আরবী ভাষার প্রাথমিক জ্ঞান থাকলে ভালো']],
                                ['name' => 'আলিম (১১-১২)', 'icon' => '🎓', 'color' => 'gold', 'age' => '১৫-১৮ বছর', 'requirements' => ['দাখিল পরীক্ষায় উত্তীর্ণ হতে হবে', 'ন্যূনতম GPA ২.০০ প্রয়োজন', 'বাংলাদেশ মাদরাসা শিক্ষা বোর্ড অনুমোদিত সনদ']],
                            ];
                            $colors = [
                                'blue' => 'bg-blue-50 border-blue-200',
                                'green' => 'bg-green-50 border-green-200',
                                'purple' => 'bg-purple-50 border-purple-200',
                                'gold' => 'bg-yellow-50 border-yellow-200',
                            ];
                        @endphp

                        @foreach($departments as $dept)
                            <div class="rounded-3xl border-2 {{ $colors[$dept['color']] }} overflow-hidden">
                                <div class="p-6 md:p-8">
                                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                                        <div class="flex items-center gap-4">
                                            <span class="text-4xl">{{ $dept['icon'] }}</span>
                                            <div>
                                                <h3 class="text-2xl font-bold text-gray-900">{{ $dept['name'] }}</h3>
                                                <span class="text-gray-600">বয়সসীমা: <strong>{{ $dept['age'] }}</strong></span>
                                            </div>
                                        </div>
                                        <a href="{{ route('admission.apply') }}"
                                            class="px-6 py-3 bg-primary-600 text-white rounded-full font-semibold hover:bg-primary-700 transition-colors">
                                            আবেদন করুন
                                        </a>
                                    </div>

                                    <div class="space-y-3">
                                        @foreach($dept['requirements'] as $req)
                                            <div class="flex items-start gap-3">
                                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-gray-700">{{ $req }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 bg-yellow-50 rounded-xl p-6 text-center">
                        <p class="text-yellow-700">
                            💡 এই পেজের কনটেন্ট কাস্টমাইজ করতে Admin Panel > ওয়েবসাইট কনটেন্ট > যোগ্যতা ট্যাবে যান
                        </p>
                    </div>
                @endif

                <!-- CTA -->
                <div class="mt-12 text-center" data-aos="fade-up">
                    <p class="text-gray-600 mb-6">আরো প্রশ্ন থাকলে আমাদের সাথে যোগাযোগ করুন</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('admission.apply') }}"
                            class="px-8 py-4 bg-gradient-to-r from-gold-400 to-gold-600 text-gray-900 rounded-full font-bold hover:shadow-lg transition-all">
                            অনলাইন আবেদন করুন
                        </a>
                        <a href="{{ route('contact') }}"
                            class="px-8 py-4 border-2 border-primary-600 text-primary-600 rounded-full font-bold hover:bg-primary-50 transition-colors">
                            যোগাযোগ করুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection