@extends('website.layouts.app')

@section('title', 'ইভেন্ট - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">ইভেন্ট ও অনুষ্ঠান</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আসন্ন ও সাম্প্রতিক কার্যক্রম</p>
        </div>
    </section>

    <!-- Upcoming Events -->
    @if($upcoming->count() > 0)
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-8" data-aos="fade-up">আসন্ন ইভেন্ট</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($upcoming as $event)
                        <div class="bg-gradient-to-br from-primary-50 to-gold-50 rounded-2xl p-6 border border-primary-100"
                            data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex flex-col items-center justify-center text-white flex-shrink-0">
                                    <span class="text-xl font-bold">{{ $event->start_date->format('d') }}</span>
                                    <span class="text-xs">{{ $event->start_date->translatedFormat('M') }}</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $event->title }}</h3>
                                    @if($event->venue)
                                        <p class="text-sm text-primary-600 mb-1">📍 {{ $event->venue }}</p>
                                    @endif
                                    <p class="text-gray-600 text-sm">{{ Str::limit($event->description, 100) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Past Events -->
    @if($past->count() > 0)
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-8" data-aos="fade-up">সাম্প্রতিক ইভেন্ট</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($past as $event)
                        <div class="bg-white rounded-xl p-4 shadow-sm" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                            <p class="text-xs text-gray-500 mb-1">{{ $event->event_date->format('d M, Y') }}</p>
                            <h4 class="font-semibold text-gray-900">{{ $event->title }}</h4>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection