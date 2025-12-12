@extends('website.layouts.app')

@section('title', $news->title . ' - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-3xl md:text-4xl font-bold mb-4 max-w-3xl mx-auto" data-aos="fade-up">{{ $news->title }}</h1>
            <p class="text-primary-200" data-aos="fade-up" data-aos-delay="100">{{ $news->created_at->format('d M, Y') }}
            </p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li><a href="{{ route('news') }}" class="hover:text-white">সংবাদ</a></li>
                    <li>/</li>
                    <li class="text-white">বিস্তারিত</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- News Content -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                @if($news->image)
                    <div class="mb-8 rounded-2xl overflow-hidden shadow-lg" data-aos="fade-up">
                        <img src="{{ Storage::url($news->image) }}" alt="{{ $news->title }}" class="w-full h-auto">
                    </div>
                @endif

                <article class="prose prose-lg max-w-none" data-aos="fade-up">
                    {!! $news->content !!}
                </article>

                <!-- Share -->
                <div class="mt-12 pt-8 border-t" data-aos="fade-up">
                    <p class="text-gray-600 mb-4">শেয়ার করুন:</p>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                            target="_blank"
                            class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($news->title) }}"
                            target="_blank"
                            class="w-10 h-10 bg-gray-900 text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($news->title . ' ' . request()->url()) }}" target="_blank"
                            class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-8" data-aos="fade-up">
                    <a href="{{ route('news') }}"
                        class="inline-flex items-center gap-2 text-primary-600 font-semibold hover:gap-3 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                        </svg>
                        সব সংবাদ দেখুন
                    </a>
                </div>
            </div>

            <!-- Related News -->
            @if($related->count() > 0)
                <div class="mt-20">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8" data-aos="fade-up">সম্পর্কিত সংবাদ</h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($related as $item)
                            <a href="{{ route('news.show', $item->slug) }}"
                                class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors group" data-aos="fade-up"
                                data-aos-delay="{{ $loop->index * 100 }}">
                                <h4 class="font-semibold text-gray-900 line-clamp-2 group-hover:text-primary-600">{{ $item->title }}
                                </h4>
                                <p class="text-sm text-gray-500 mt-2">{{ $item->created_at->format('d M, Y') }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection