@extends('website.layouts.app')

@section('title', 'ডাউনলোড - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">ডাউনলোড সেন্টার</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">ফর্ম, সিলেবাস ও অন্যান্য</p>
        </div>
    </section>

    <!-- Downloads -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                @forelse($downloads as $category => $downloadGroup)
                    <div class="mb-8" data-aos="fade-up">
                        <h2
                            class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b border-primary-200 flex items-center gap-2">
                            <span class="w-8 h-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                                📁
                            </span>
                            {{ $category }}
                        </h2>
                        <div class="space-y-3">
                            @foreach($downloadGroup as $download)
                                <a href="{{ Storage::url($download->file) }}" target="_blank"
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 group-hover:text-primary-600">
                                                {{ $download->title }}</h4>
                                            @if($download->description)
                                                <p class="text-sm text-gray-500">{{ $download->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-primary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span class="font-medium text-sm">ডাউনলোড</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div class="text-6xl mb-4">📥</div>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">কোন ফাইল পাওয়া যায়নি</h3>
                        <p class="text-gray-500">শীঘ্রই আপডেট করা হবে</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection