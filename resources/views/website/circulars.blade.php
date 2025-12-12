@extends('website.layouts.app')

@section('title', 'সার্কুলার - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">সার্কুলার ও নোটিশ</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">সাম্প্রতিক নোটিশ</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                @forelse($circulars as $circular)
                    <div class="bg-gray-50 rounded-xl p-6 mb-4 hover:shadow-md transition-shadow" data-aos="fade-up">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">{{ $circular->issue_date->format('d M, Y') }}</p>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $circular->title }}</h3>
                            </div>
                            @if($circular->file)
                                <a href="{{ Storage::url($circular->file) }}" target="_blank"
                                    class="flex-shrink-0 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
                                    ডাউনলোড
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div class="text-6xl mb-4">📋</div>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">কোন সার্কুলার পাওয়া যায়নি</h3>
                    </div>
                @endforelse

                {{ $circulars->links() }}
            </div>
        </div>
    </section>
@endsection