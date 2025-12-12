@extends('website.layouts.app')

@section('title', 'ফি স্ট্রাকচার - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section class="pt-32 pb-20" style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">ফি স্ট্রাকচার</h1>
            <p class="text-xl opacity-80" data-aos="fade-up" data-aos-delay="100">বিভাগ অনুযায়ী ফি বিবরণী</p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li><a href="{{ route('admission') }}" class="hover:text-white">ভর্তি</a></li>
                    <li>/</li>
                    <li class="text-white">ফি</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Fees Content -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">

                <!-- Pricing Cards -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    @php
                        $feeStructures = [
                            [
                                'name' => 'নাজেরা বিভাগ',
                                'icon' => '📖',
                                'popular' => false,
                                'admission' => '১,০০০',
                                'monthly' => '৫০০',
                                'annual' => '৫,০০০',
                                'includes' => ['বই ও খাতা', 'পরীক্ষা ফি', 'আইডি কার্ড'],
                            ],
                            [
                                'name' => 'হিফজ বিভাগ',
                                'icon' => '🕌',
                                'popular' => true,
                                'admission' => '২,০০০',
                                'monthly' => '১,০০০',
                                'annual' => '১০,০০০',
                                'includes' => ['বই ও খাতা', 'পরীক্ষা ফি', 'আইডি কার্ড', 'বিশেষ তদারকি'],
                            ],
                            [
                                'name' => 'কিতাব বিভাগ',
                                'icon' => '📚',
                                'popular' => false,
                                'admission' => '১,৫০০',
                                'monthly' => '৮০০',
                                'annual' => '৮,০০০',
                                'includes' => ['বই ও খাতা', 'পরীক্ষা ফি', 'আইডি কার্ড'],
                            ],
                            [
                                'name' => 'আলিম (১১-১২)',
                                'icon' => '🎓',
                                'popular' => false,
                                'admission' => '৩,০০০',
                                'monthly' => '১,২০০',
                                'annual' => '১২,০০০',
                                'includes' => ['বোর্ড রেজিস্ট্রেশন', 'পরীক্ষা ফি', 'আইডি কার্ড', 'লাইব্রেরি'],
                            ],
                            [
                                'name' => 'ফাযিল (ডিগ্রী)',
                                'icon' => '🏛️',
                                'popular' => false,
                                'admission' => '৫,০০০',
                                'monthly' => '১,৫০০',
                                'annual' => '১৫,০০০',
                                'includes' => ['বিশ্ববিদ্যালয় রেজিস্ট্রেশন', 'পরীক্ষা ফি', 'আইডি কার্ড', 'লাইব্রেরি', 'কম্পিউটার ল্যাব'],
                            ],
                            [
                                'name' => 'আবাসিক (হোস্টেল)',
                                'icon' => '🏠',
                                'popular' => false,
                                'admission' => '৫,০০০',
                                'monthly' => '৩,০০০',
                                'annual' => '৩০,০০০',
                                'includes' => ['থাকা', 'খাওয়া (৩ বেলা)', 'বিদ্যুৎ', 'পানি', 'নিরাপত্তা'],
                            ],
                        ];
                    @endphp

                    @foreach($feeStructures as $fee)
                        <div class="relative bg-white rounded-3xl shadow-xl overflow-hidden hover:-translate-y-2 transition-transform"
                            data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            @if($fee['popular'])
                                <div class="absolute top-4 right-4 px-4 py-1 bg-gold-500 text-white text-sm font-bold rounded-full">
                                    জনপ্রিয়
                                </div>
                            @endif

                            <div class="p-8">
                                <div class="text-center mb-6">
                                    <span class="text-5xl">{{ $fee['icon'] }}</span>
                                    <h3 class="text-xl font-bold text-gray-900 mt-4">{{ $fee['name'] }}</h3>
                                </div>

                                <div class="text-center mb-6 pb-6 border-b border-gray-100">
                                    <p class="text-gray-500 text-sm">মাসিক ফি</p>
                                    <p class="text-4xl font-bold text-primary-600">
                                        ৳{{ $fee['monthly'] }}
                                        <span class="text-base font-normal text-gray-500">/মাস</span>
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                                        <p class="text-xs text-gray-500">ভর্তি ফি</p>
                                        <p class="font-bold text-gray-900">৳{{ $fee['admission'] }}</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                                        <p class="text-xs text-gray-500">বার্ষিক</p>
                                        <p class="font-bold text-gray-900">৳{{ $fee['annual'] }}</p>
                                    </div>
                                </div>

                                <div class="space-y-3 mb-8">
                                    @foreach($fee['includes'] as $item)
                                        <div class="flex items-center gap-3 text-gray-600">
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>{{ $item }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <a href="{{ route('admission.apply') }}"
                                    class="block w-full py-3 {{ $fee['popular'] ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700' }} rounded-xl font-semibold text-center hover:opacity-90 transition-opacity">
                                    আবেদন করুন
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Additional Fees Note -->
                <div class="bg-gold-50 border border-gold-200 rounded-3xl p-8 mb-12" data-aos="fade-up">
                    <h3 class="text-xl font-bold text-gold-800 mb-4 flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        গুরুত্বপূর্ণ তথ্য
                    </h3>
                    <ul class="space-y-2 text-gold-800">
                        <li class="flex items-start gap-2">
                            <span>•</span>
                            <span>ভর্তি ফি শুধুমাত্র একবার প্রদেয়</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>•</span>
                            <span>মাসিক ফি প্রতি মাসের ১০ তারিখের মধ্যে পরিশোধযোগ্য</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>•</span>
                            <span>বিলম্বে পরিশোধে ৫০ টাকা জরিমানা প্রযোজ্য</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>•</span>
                            <span>গরীব ও মেধাবী ছাত্রদের জন্য বিশেষ ছাড় প্রযোজ্য</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>•</span>
                            <span>একই পরিবারের একাধিক ছাত্রের ক্ষেত্রে ১০% ছাড়</span>
                        </li>
                    </ul>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-3xl shadow-xl p-8" data-aos="fade-up">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">পেমেন্ট পদ্ধতি</h3>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-pink-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <img src="https://www.bkash.com/sites/all/themes/flavor/images/bkash-logo.png" alt="bKash"
                                    class="h-12" onerror="this.innerHTML='<span class=\'text-3xl\'>📱</span>'">
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">bKash</h4>
                            <p class="text-gray-600">০১XXXXXXXXX</p>
                            <p class="text-sm text-gray-500">মার্চেন্ট / পার্সোনাল</p>
                        </div>
                        <div class="text-center">
                            <div class="w-20 h-20 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">📲</span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">Nagad</h4>
                            <p class="text-gray-600">০১XXXXXXXXX</p>
                            <p class="text-sm text-gray-500">পার্সোনাল</p>
                        </div>
                        <div class="text-center">
                            <div class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🏦</span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">ব্যাংক</h4>
                            <p class="text-gray-600">সরাসরি অফিসে প্রদান</p>
                            <p class="text-sm text-gray-500">নগদ / চেক</p>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="mt-12 text-center" data-aos="fade-up">
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('admission.apply') }}"
                            class="px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-full font-bold shadow-lg hover:shadow-xl transition-all">
                            অনলাইন আবেদন করুন
                        </a>
                        <a href="{{ route('contact') }}"
                            class="px-8 py-4 border-2 border-primary-600 text-primary-600 rounded-full font-bold hover:bg-primary-50 transition-colors">
                            আরো জানতে যোগাযোগ করুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection