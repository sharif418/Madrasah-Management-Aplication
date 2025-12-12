@extends('website.layouts.app')

@section('title', 'গ্যালারি - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">ফটো গ্যালারি</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আমাদের স্মৃতিচারণ</p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li class="text-white">গ্যালারি</li>
                </ol>
            </nav>
        </div>
    </section>
    
    <!-- Albums Grid -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($albums as $album)
                    <a href="{{ route('gallery.show', $album) }}" class="group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="relative h-64 rounded-2xl overflow-hidden shadow-lg">
                            @if($album->cover_image)
                                <img src="{{ Storage::url($album->cover_image) }}" alt="{{ $album->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <h3 class="text-xl font-bold text-white mb-1">{{ $album->title }}</h3>
                                <p class="text-white/70 text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $album->photos_count }} ছবি
                                </p>
                            </div>
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-primary-600/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="px-6 py-3 bg-white text-primary-700 rounded-full font-semibold">
                                    অ্যালবাম দেখুন
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-20">
                        <div class="text-6xl mb-4">📷</div>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">কোন অ্যালবাম পাওয়া যায়নি</h3>
                        <p class="text-gray-500">শীঘ্রই আপডেট করা হবে</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($albums->hasPages())
                <div class="mt-12">
                    {{ $albums->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
