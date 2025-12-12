@extends('website.layouts.app')

@section('title', $album->title . ' - গ্যালারি')

@section('content')
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">{{ $album->title }}</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">{{ $album->photos->count() }} ছবি
            </p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li><a href="{{ route('gallery') }}" class="hover:text-white">গ্যালারি</a></li>
                    <li>/</li>
                    <li class="text-white">{{ Str::limit($album->title, 20) }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            @if($album->description)
                <p class="text-center text-gray-600 mb-8 max-w-2xl mx-auto" data-aos="fade-up">{{ $album->description }}</p>
            @endif

            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" x-data="{ lightbox: null }">
                @forelse($album->photos as $photo)
                    <div @click="lightbox = '{{ Storage::url($photo->image) }}'" class="cursor-pointer group" data-aos="fade-up"
                        data-aos-delay="{{ ($loop->index % 4) * 50 }}">
                        <div class="aspect-square rounded-xl overflow-hidden shadow-md">
                            <img src="{{ Storage::url($photo->image) }}" alt="{{ $photo->caption ?? $album->title }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <p class="text-gray-500">এই অ্যালবামে কোন ছবি নেই</p>
                    </div>
                @endforelse

                <!-- Lightbox -->
                <div x-show="lightbox" @click="lightbox = null"
                    class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4" x-transition>
                    <img :src="lightbox" class="max-w-full max-h-full rounded-lg">
                    <button @click="lightbox = null" class="absolute top-4 right-4 text-white text-4xl">&times;</button>
                </div>
            </div>

            <div class="text-center mt-10" data-aos="fade-up">
                <a href="{{ route('gallery') }}" class="inline-flex items-center gap-2 text-primary-600 font-semibold">
                    ← সব অ্যালবাম
                </a>
            </div>
        </div>
    </section>
@endsection