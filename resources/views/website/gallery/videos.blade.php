@extends('website.layouts.app')

@section('title', 'ভিডিও গ্যালারি - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section class="pt-32 pb-20" style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">ভিডিও গ্যালারি</h1>
            <p class="text-xl opacity-80" data-aos="fade-up" data-aos-delay="100">প্রতিষ্ঠানের বিভিন্ন অনুষ্ঠানের ভিডিও</p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li><a href="{{ route('gallery') }}" class="hover:text-white">গ্যালারি</a></li>
                    <li>/</li>
                    <li class="text-white">ভিডিও</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Videos Content -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">

                @php
                    // Sample videos - replace with actual data from database
                    $videos = [
                        [
                            'title' => 'বার্ষিক ফলাফল প্রকাশ ২০২৪',
                            'youtube_id' => 'dQw4w9WgXcQ', // Replace with actual YouTube IDs
                            'date' => '১৫ ডিসেম্বর, ২০২৪',
                            'views' => '১.২K',
                        ],
                        [
                            'title' => 'হিফজুল কুরআন সমাপনী অনুষ্ঠান',
                            'youtube_id' => 'dQw4w9WgXcQ',
                            'date' => '১০ নভেম্বর, ২০২৪',
                            'views' => '২.৫K',
                        ],
                        [
                            'title' => 'বার্ষিক সাংস্কৃতিক অনুষ্ঠান',
                            'youtube_id' => 'dQw4w9WgXcQ',
                            'date' => '২০ অক্টোবর, ২০২৪',
                            'views' => '৮৫০',
                        ],
                        [
                            'title' => 'আন্তঃমাদরাসা কুইজ প্রতিযোগিতা',
                            'youtube_id' => 'dQw4w9WgXcQ',
                            'date' => '৫ সেপ্টেম্বর, ২০২৪',
                            'views' => '৬২০',
                        ],
                        [
                            'title' => 'প্রতিষ্ঠান পরিদর্শন - ২০২৪',
                            'youtube_id' => 'dQw4w9WgXcQ',
                            'date' => '১ আগস্ট, ২০২৪',
                            'views' => '১.৮K',
                        ],
                        [
                            'title' => 'ভর্তি প্রক্রিয়া ও সুযোগ-সুবিধা',
                            'youtube_id' => 'dQw4w9WgXcQ',
                            'date' => '১৫ জানুয়ারি, ২০২৪',
                            'views' => '৩.২K',
                        ],
                    ];
                @endphp

                <!-- Video Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($videos as $video)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover-lift" data-aos="fade-up"
                            data-aos-delay="{{ $loop->index * 100 }}" x-data="{ playing: false }">

                            <!-- Video Thumbnail -->
                            <div class="relative aspect-video bg-gray-900">
                                <template x-if="!playing">
                                    <div class="absolute inset-0">
                                        <img src="https://img.youtube.com/vi/{{ $video['youtube_id'] }}/maxresdefault.jpg"
                                            alt="{{ $video['title'] }}" class="w-full h-full object-cover"
                                            onerror="this.src='https://img.youtube.com/vi/{{ $video['youtube_id'] }}/hqdefault.jpg'">
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                            <button @click="playing = true"
                                                class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center transition-transform hover:scale-110">
                                                <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="playing">
                                    <iframe class="absolute inset-0 w-full h-full"
                                        src="https://www.youtube.com/embed/{{ $video['youtube_id'] }}?autoplay=1"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                </template>
                            </div>

                            <!-- Video Info -->
                            <div class="p-5">
                                <h3 class="font-bold text-gray-900 text-lg mb-2 line-clamp-2">{{ $video['title'] }}</h3>
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $video['date'] }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ $video['views'] }} views
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- YouTube Channel Link -->
                <div class="mt-16 text-center" data-aos="fade-up">
                    <div class="bg-red-50 rounded-3xl p-8 inline-block">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div class="w-20 h-20 bg-red-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                            </div>
                            <div class="text-center md:text-left">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">আমাদের YouTube চ্যানেল</h3>
                                <p class="text-gray-600 mb-4">সকল আপডেট পেতে চ্যানেলটি সাবস্ক্রাইব করুন</p>
                                <a href="#" target="_blank"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-full font-semibold hover:bg-red-700 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                    </svg>
                                    Subscribe
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection