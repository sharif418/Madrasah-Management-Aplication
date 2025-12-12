@extends('website.layouts.app')

@section('title', 'সংবাদ - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">সংবাদ ও নোটিশ</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">সাম্প্রতিক আপডেট</p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li class="text-white">সংবাদ</li>
                </ol>
            </nav>
        </div>
    </section>
    
    <!-- News Grid -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($news as $item)
                    <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover-lift" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
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
                @empty
                    <div class="col-span-full text-center py-20">
                        <div class="text-6xl mb-4">📰</div>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">কোন সংবাদ পাওয়া যায়নি</h3>
                        <p class="text-gray-500">শীঘ্রই আপডেট করা হবে</p>
                    </div>
                @endforelse
            </div>
            
            @if($news->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $news->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
