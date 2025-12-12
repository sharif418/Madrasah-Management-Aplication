@extends('website.layouts.app')

@section('title', 'বিভাগসমূহ - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">বিভাগসমূহ</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আমাদের শিক্ষা বিভাগ</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($departments as $dept)
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover-lift" data-aos="fade-up"
                        data-aos-delay="{{ $loop->index * 100 }}">
                        <div
                            class="w-16 h-16 bg-primary-100 text-primary-600 rounded-2xl flex items-center justify-center mb-4">
                            <span class="text-3xl">📚</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $dept->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $dept->description ?? 'শিক্ষা বিভাগ' }}</p>
                        <div class="flex items-center gap-2 text-primary-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" />
                            </svg>
                            <span class="font-semibold">{{ $dept->students_count }} জন ছাত্র</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <div class="text-6xl mb-4">📚</div>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">বিভাগ পাওয়া যায়নি</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection