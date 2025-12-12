@extends('website.layouts.app')

@section('title', 'শিক্ষক তালিকা - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">শিক্ষক তালিকা</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আমাদের যোগ্য শিক্ষকমণ্ডলী</p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li><a href="{{ route('about') }}" class="hover:text-white">পরিচিতি</a></li>
                    <li>/</li>
                    <li class="text-white">শিক্ষক তালিকা</li>
                </ol>
            </nav>
        </div>
    </section>
    
    <!-- Teachers Grid -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            @forelse($teachers as $designation => $teacherGroup)
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-2 border-b-2 border-primary-500 inline-block" data-aos="fade-up">
                        {{ $designation }}
                    </h2>
                    
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($teacherGroup as $teacher)
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover-lift group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                <div class="relative h-64 overflow-hidden bg-gray-100">
                                    @if($teacher->photo)
                                        <img src="{{ Storage::url($teacher->photo) }}" alt="{{ $teacher->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                            <span class="text-6xl text-primary-400">👨‍🏫</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </div>
                                <div class="p-4 text-center">
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $teacher->name }}</h3>
                                    <p class="text-primary-600 text-sm">{{ $teacher->designation }}</p>
                                    @if($teacher->qualification)
                                        <p class="text-gray-500 text-xs mt-1">{{ $teacher->qualification }}</p>
                                    @endif
                                    @if($teacher->phone)
                                        <p class="text-gray-400 text-xs mt-2">📞 {{ $teacher->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">👨‍🏫</div>
                    <h3 class="text-2xl font-bold text-gray-400 mb-2">শিক্ষক তালিকা পাওয়া যায়নি</h3>
                    <p class="text-gray-500">শীঘ্রই আপডেট করা হবে</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
