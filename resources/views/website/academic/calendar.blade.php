@extends('website.layouts.app')

@section('title', 'শিক্ষাপঞ্জিকা - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <x-website.page-header title="একাডেমিক ক্যালেন্ডার" subtitle="সারা বছরের ইভেন্ট ও ছুটির তালিকা" />

    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                
                @if($events->count() > 0)
                    <div class="space-y-12">
                        @foreach($events as $month => $monthEvents)
                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden" data-aos="fade-up">
                                <!-- Month Header -->
                                <div class="bg-primary-600 px-8 py-4 flex justify-between items-center text-white">
                                    <h2 class="text-2xl font-bold">{{ $month }}</h2>
                                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">{{ $monthEvents->count() }} টি ইভেন্ট</span>
                                </div>
                                
                                <!-- Events List -->
                                <div class="divide-y divide-gray-100">
                                    @foreach($monthEvents as $event)
                                        <div class="p-6 md:p-8 flex flex-col md:flex-row gap-6 hover:bg-gray-50 transition-colors group">
                                            <!-- Date Badge -->
                                            <div class="flex-shrink-0 text-center md:text-left">
                                                <div class="w-20 md:w-24 h-20 md:h-24 bg-primary-50 rounded-2xl flex flex-col items-center justify-center border-2 border-primary-100 group-hover:border-primary-500 transition-colors mx-auto md:mx-0">
                                                    <span class="text-3xl font-bold text-primary-700">{{ $event->start_date->format('d') }}</span>
                                                    <span class="text-sm font-medium text-primary-600 uppercase">{{ $event->start_date->format('M') }}</span>
                                                </div>
                                            </div>

                                            <div class="flex-grow">
                                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                                    @if($event->is_holiday)
                                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-bold">ছুটি</span>
                                                    @endif
                                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold">
                                                        {{ \App\Models\Event::typeOptions()[$event->type] ?? ucwords($event->type) }}
                                                    </span>
                                                </div>
                                                
                                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">{{ $event->title }}</h3>
                                                <p class="text-gray-600 text-sm leading-relaxed mb-3">{{ $event->description }}</p>
                                                
                                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                                    @if($event->start_date != $event->end_date && $event->end_date)
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            {{ $event->date_range }}
                                                        </span>
                                                    @endif
                                                    
                                                    @if($event->venue)
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                            {{ $event->venue }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 bg-white rounded-3xl shadow-sm">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">কোন ইভেন্ট পাওয়া যায়নি</h3>
                        <p class="text-gray-500 mt-2">বর্তমানে একাডেমিক ক্যালেন্ডারে কোনো তথ্য নেই।</p>
                    </div>
                @endif

                <!-- Download Button -->
                <div class="mt-12 text-center" data-aos="fade-up">
                    <a href="{{ route('downloads') }}"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-white border-2 border-primary-600 text-primary-600 rounded-full font-bold hover:bg-primary-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        ক্যালেন্ডার ডাউনলোড (PDF)
                    </a>
                </div>

            </div>
        </div>
    </section>
@endsection