@extends('website.layouts.app')

@section('title', 'ক্লাস রুটিন - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <x-website.page-header title="ক্লাস রুটিন" subtitle="সাপ্তাহিক ক্লাস সময়সূচী" />

    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            @if($classes->count() > 0)
                <div x-data="{ selectedClass: {{ $classes->first()->id }} }">
                    
                    <!-- Class Filter -->
                    <div class="mb-10 flex flex-wrap gap-3 justify-center" data-aos="fade-up">
                        @foreach($classes as $class)
                            <button @click="selectedClass = {{ $class->id }}"
                                :class="selectedClass === {{ $class->id }} ? 'bg-primary-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-100 shadow-sm'"
                                class="px-6 py-3 rounded-full font-semibold transition-all transform hover:-translate-y-1">
                                {{ $class->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Routines -->
                    @foreach($classes as $class)
                        <div x-show="selectedClass === {{ $class->id }}" x-cloak class="space-y-8" data-aos="fade-up">
                            @php
                                $classRoutines = $routines[$class->id] ?? collect();
                                $days = [
                                    'saturday' => 'শনিবার',
                                    'sunday' => 'রবিবার',
                                    'monday' => 'সোমবার',
                                    'tuesday' => 'মঙ্গলবার',
                                    'wednesday' => 'বুধবার',
                                    'thursday' => 'বৃহস্পতিবার',
                                    'friday' => 'শুক্রবার'
                                ];
                            @endphp

                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($days as $key => $dayLabel)
                                    @php $dayRoutines = $classRoutines[$key] ?? collect(); @endphp
                                    
                                    <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-primary-500 hover:shadow-xl transition-shadow">
                                        <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $dayLabel }}
                                        </h3>
                                        
                                        @if($dayRoutines->count() > 0)
                                            <div class="space-y-4">
                                                @foreach($dayRoutines as $routine)
                                                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 hover:bg-primary-50 transition-colors">
                                                        <div class="bg-white p-2 rounded-lg shadow-sm text-center min-w-[70px]">
                                                            <span class="block text-xs text-gray-500">শুরু</span>
                                                            <span class="block font-bold text-primary-700 text-sm">
                                                                {{ \Carbon\Carbon::parse($routine->start_time)->format('h:i') }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-bold text-gray-900">{{ $routine->subject->name ?? 'N/A' }}</h4>
                                                            <p class="text-sm text-gray-600 flex items-center gap-1 mt-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                                {{ $routine->teacher->name ?? 'শিক্ষক' }}
                                                            </p>
                                                            @if($routine->room)
                                                            <p class="text-xs text-gray-500 mt-1 bg-gray-200 inline-block px-2 py-0.5 rounded">
                                                                রুম: {{ $routine->room }}
                                                            </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-8 text-gray-400">
                                                <p>এই দিন কোন ক্লাস নেই</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
            @else
                <div class="text-center py-20">
                    <h3 class="text-xl text-gray-600">বর্তমানে কোনো রুটিন প্রকাশিত হয়নি।</h3>
                </div>
            @endif
        </div>
    </section>
@endsection