@extends('website.layouts.app')

@section('title', institution_name() ?? 'মাদরাসা - হোম')

@section('content')
    <!-- Hero Section with Slider -->
    <section class="relative h-screen min-h-[600px] gradient-primary hero-pattern overflow-hidden">
        <!-- Slider -->
        <div x-data="{ current: 0, slides: {{ $sliders->count() ?: 1 }} }" x-init="setInterval(() => current = (current + 1) % slides, 5000)" class="absolute inset-0">
            @forelse($sliders as $index => $slider)
                <div 
                    x-show="current === {{ $index }}"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 scale-105"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                >
                    @if($slider->image)
                        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ Storage::url($slider->image) }}')">
                            <div class="absolute inset-0 bg-gradient-to-r from-primary-900/90 via-primary-800/80 to-transparent"></div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="absolute inset-0 bg-gradient-to-br from-primary-700 via-primary-800 to-primary-900"></div>
            @endforelse
        </div>
        
        <!-- Hero Content -->
        <div class="relative container mx-auto px-4 h-full flex items-center">
            <div class="max-w-3xl text-white pt-20">
                <p class="text-gold-300 text-lg mb-2 font-medium" data-aos="fade-up">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</p>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-4" data-aos="fade-up" data-aos-delay="100">
                    {{ institution_name() ?? 'মাদরাসা নাম' }}
                </h1>
                <p class="text-xl md:text-2xl text-primary-100 mb-8" data-aos="fade-up" data-aos-delay="200">
                    কুরআন ও সুন্নাহর আলোকে দ্বীনি ও আধুনিক শিক্ষার সমন্বয়
                </p>
                <div class="flex flex-wrap gap-4" data-aos="fade-up" data-aos-delay="300">
                    <a href="{{ route('admission') }}" class="px-8 py-4 bg-gradient-to-r from-gold-400 to-gold-600 text-gray-900 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        ভর্তি আবেদন করুন
                    </a>
                    <a href="{{ route('about') }}" class="px-8 py-4 bg-white/20 backdrop-blur text-white rounded-full font-bold text-lg border border-white/30 hover:bg-white/30 transition-all duration-300">
                        আমাদের সম্পর্কে
                    </a>
                </div>

                <!-- Prayer Times (Mobile) -->
                <div class="block lg:hidden mt-8 max-w-sm" data-aos="fade-up" data-aos-delay="400">
                     @include('website.layouts.partials.prayer-times')
                </div>
            </div>
            
            <!-- Prayer Times Widget (Desktop) -->
            <div class="hidden lg:block absolute right-8 top-1/2 -translate-y-1/2 w-72" data-aos="fade-left" data-aos-delay="400">
                @include('website.layouts.partials.prayer-times')
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce hidden md:block">
            <svg class="w-8 h-8 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
    </section>
    
    <!-- Live Stats Section -->
    <section class="py-16 -mt-20 relative z-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Students -->
                <div class="bg-white rounded-2xl shadow-xl p-6 text-center hover-lift" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-4xl font-bold text-primary-700 mb-1" x-data="{ count: 0 }" x-init="let target = {{ $stats['students'] }}; let step = target / 50; const timer = setInterval(() => { count += step; if(count >= target) { count = target; clearInterval(timer); } }, 30)" x-text="Math.floor(count)">
                        {{ $stats['students'] }}
                    </h3>
                    <p class="text-gray-600">ছাত্র সংখ্যা</p>
                </div>
                
                <!-- Teachers -->
                <div class="bg-white rounded-2xl shadow-xl p-6 text-center hover-lift" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gold-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-4xl font-bold text-gold-600 mb-1" x-data="{ count: 0 }" x-init="let target = {{ $stats['teachers'] }}; let step = target / 50; const timer = setInterval(() => { count += step; if(count >= target) { count = target; clearInterval(timer); } }, 30)" x-text="Math.floor(count)">
                        {{ $stats['teachers'] }}
                    </h3>
                    <p class="text-gray-600">শিক্ষক</p>
                </div>
                
                <!-- Staff -->
                <div class="bg-white rounded-2xl shadow-xl p-6 text-center hover-lift" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-4xl font-bold text-blue-600 mb-1" x-data="{ count: 0 }" x-init="let target = {{ $stats['staff'] }}; let step = target / 50; const timer = setInterval(() => { count += step; if(count >= target) { count = target; clearInterval(timer); } }, 30)" x-text="Math.floor(count)">
                        {{ $stats['staff'] }}
                    </h3>
                    <p class="text-gray-600">কর্মচারী</p>
                </div>
                
                <!-- Classes -->
                <div class="bg-white rounded-2xl shadow-xl p-6 text-center hover-lift" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-4xl font-bold text-purple-600 mb-1" x-data="{ count: 0 }" x-init="let target = {{ $stats['classes'] }}; let step = target / 50; const timer = setInterval(() => { count += step; if(count >= target) { count = target; clearInterval(timer); } }, 30)" x-text="Math.floor(count)">
                        {{ $stats['classes'] }}
                    </h3>
                    <p class="text-gray-600">বিভাগ/শ্রেণী</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Welcome Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <p class="text-primary-600 font-semibold mb-2">আমাদের সম্পর্কে</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                        স্বাগতম <span class="text-primary-600">{{ institution_name() ?? 'মাদরাসা' }}</span>-তে
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        আমাদের প্রতিষ্ঠান কুরআন ও সুন্নাহর আলোকে দ্বীনি ও আধুনিক শিক্ষার সমন্বয়ে একটি আদর্শ শিক্ষা প্রতিষ্ঠান। 
                        এখানে ছাত্ররা হিফজুল কুরআন, ইলমে দ্বীন এবং আধুনিক শিক্ষার মাধ্যমে নিজেদের গড়ে তোলার সুযোগ পায়।
                    </p>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">হিফজ বিভাগ</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">কিতাব বিভাগ</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">যোগ্য শিক্ষকমণ্ডলী</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">আধুনিক সুযোগ-সুবিধা</span>
                        </div>
                    </div>
                    <a href="{{ route('about') }}" class="inline-flex items-center gap-2 text-primary-600 font-semibold hover:gap-3 transition-all">
                        আরও জানুন
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
                <div class="relative" data-aos="fade-left">
                    <div class="bg-primary-100 rounded-3xl p-8">
                        <div class="bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl p-10 text-center text-white">
                            <div class="text-6xl mb-4">🕌</div>
                            <h3 class="text-2xl font-bold mb-2">আমাদের লক্ষ্য</h3>
                            <p class="text-primary-100">
                                কুরআন-সুন্নাহর আলোকে আদর্শ মানুষ তৈরি করা এবং দ্বীন ও দুনিয়া উভয় ক্ষেত্রে সফল মুসলিম গড়ে তোলা।
                            </p>
                        </div>
                    </div>
                    <!-- Decorative Elements -->
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-gold-200 rounded-full opacity-50"></div>
                    <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-primary-200 rounded-full opacity-50"></div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Latest News Section -->
    @if($news->count() > 0)
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <p class="text-primary-600 font-semibold mb-2" data-aos="fade-up">সাম্প্রতিক সংবাদ</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900" data-aos="fade-up" data-aos-delay="100">
                    আপডেট ও সংবাদ
                </h2>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($news->take(6) as $item)
                    <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover-lift" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        @if($item->image)
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            </div>
                        @else
                            <div class="h-48 bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="p-6">
                            <p class="text-sm text-gray-500 mb-2">{{ $item->created_at->format('d M, Y') }}</p>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $item->title }}</h3>
                            <p class="text-gray-600 text-sm line-clamp-3 mb-4">{{ Str::limit(strip_tags($item->content), 120) }}</p>
                            <a href="{{ route('news.show', $item->slug) }}" class="text-primary-600 font-semibold text-sm hover:text-primary-700">
                                বিস্তারিত পড়ুন →
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            
            <div class="text-center mt-10">
                <a href="{{ route('news') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-primary-600 text-white rounded-full font-semibold hover:bg-primary-700 transition-colors">
                    সব সংবাদ দেখুন
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif
    
    <!-- Upcoming Events -->
    @if($events->count() > 0)
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <p class="text-gold-600 font-semibold mb-2" data-aos="fade-up">আসন্ন কার্যক্রম</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900" data-aos="fade-up" data-aos-delay="100">
                    ইভেন্ট ক্যালেন্ডার
                </h2>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($events as $event)
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex flex-col items-center justify-center text-white flex-shrink-0">
                                <span class="text-xl font-bold">{{ $event->start_date->format('d') }}</span>
                                <span class="text-xs">{{ $event->start_date->translatedFormat('M') }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">{{ $event->title }}</h4>
                                <p class="text-sm text-gray-500">{{ Str::limit($event->description, 60) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    
    <!-- Testimonials -->
    @if($testimonials->count() > 0)
    <section class="py-20 gradient-primary hero-pattern">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12 text-white">
                <p class="text-gold-300 font-semibold mb-2" data-aos="fade-up">মতামত</p>
                <h2 class="text-3xl md:text-4xl font-bold" data-aos="fade-up" data-aos-delay="100">
                    অভিভাবক ও ছাত্রদের মন্তব্য
                </h2>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/20" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="flex items-center gap-4 mb-4">
                            @if($testimonial->photo)
                                <img src="{{ Storage::url($testimonial->photo) }}" alt="{{ $testimonial->name }}" class="w-14 h-14 rounded-full object-cover">
                            @else
                                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-white text-xl font-bold">
                                    {{ mb_substr($testimonial->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-white">{{ $testimonial->name }}</h4>
                                <p class="text-sm text-primary-200">{{ $testimonial->designation ?? 'অভিভাবক' }}</p>
                            </div>
                        </div>
                        <p class="text-primary-100 leading-relaxed">
                            "{{ Str::limit($testimonial->content, 150) }}"
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    
    <!-- CTA Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-3xl p-12 text-center text-white relative overflow-hidden">
                <!-- Decorative -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                
                <div class="relative z-10" data-aos="zoom-in">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">
                        আপনার সন্তানকে আদর্শ মানুষ হিসেবে গড়ে তুলুন
                    </h2>
                    <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                        আজই ভর্তি আবেদন করুন এবং আপনার সন্তানের উজ্জ্বল ভবিষ্যৎ নিশ্চিত করুন
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('admission') }}" class="px-8 py-4 bg-gold-400 text-gray-900 rounded-full font-bold text-lg shadow-xl hover:bg-gold-500 transition-colors">
                            ভর্তি তথ্য
                        </a>
                        <a href="{{ route('contact') }}" class="px-8 py-4 bg-white/20 backdrop-blur text-white rounded-full font-bold text-lg border border-white/30 hover:bg-white/30 transition-colors">
                            যোগাযোগ করুন
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
