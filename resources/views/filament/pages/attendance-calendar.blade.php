<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        <form wire:submit="loadCalendar">
            {{ $this->form }}

            <div class="mt-4">
                <x-filament::button type="submit" icon="heroicon-o-calendar">
                    ক্যালেন্ডার দেখুন
                </x-filament::button>
            </div>
        </form>
    </div>

    @if($calendarData)
        {{-- Calendar Header --}}
        <div class="mb-4 flex items-center justify-between">
            <button type="button" wire:click="previousMonth"
                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <x-heroicon-o-chevron-left class="w-6 h-6 text-gray-600 dark:text-gray-400" />
            </button>

            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ $calendarData['month_name'] }} {{ $calendarData['year'] }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $calendarData['class']->name ?? '' }}
                    @if($calendarData['is_student_view'] && $calendarData['student'])
                        - {{ $calendarData['student']->name }}
                    @endif
                </p>
            </div>

            <button type="button" wire:click="nextMonth"
                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <x-heroicon-o-chevron-right class="w-6 h-6 text-gray-600 dark:text-gray-400" />
            </button>
        </div>

        {{-- Summary Stats (for student view) --}}
        @if($calendarData['is_student_view'])
            <div class="grid grid-cols-5 gap-3 mb-6">
                <div
                    class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-center border border-blue-200 dark:border-blue-700">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $calendarData['summary']['total_days'] }}</div>
                    <div class="text-xs text-blue-700 dark:text-blue-300">মোট দিন</div>
                </div>
                <div
                    class="p-3 bg-green-50 dark:bg-green-900/30 rounded-lg text-center border border-green-200 dark:border-green-700">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $calendarData['summary']['present'] }}
                    </div>
                    <div class="text-xs text-green-700 dark:text-green-300">উপস্থিত</div>
                </div>
                <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-lg text-center border border-red-200 dark:border-red-700">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $calendarData['summary']['absent'] }}
                    </div>
                    <div class="text-xs text-red-700 dark:text-red-300">অনুপস্থিত</div>
                </div>
                <div
                    class="p-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg text-center border border-yellow-200 dark:border-yellow-700">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $calendarData['summary']['late'] }}
                    </div>
                    <div class="text-xs text-yellow-700 dark:text-yellow-300">বিলম্বে</div>
                </div>
                <div
                    class="p-3 bg-primary-50 dark:bg-primary-900/30 rounded-lg text-center border border-primary-200 dark:border-primary-700">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ $calendarData['summary']['percentage'] }}%</div>
                    <div class="text-xs text-primary-700 dark:text-primary-300">উপস্থিতি</div>
                </div>
            </div>
        @endif

        {{-- Legend --}}
        <div class="mb-4 flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-green-500"></span>
                <span class="text-gray-600 dark:text-gray-400">উপস্থিত</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-red-500"></span>
                <span class="text-gray-600 dark:text-gray-400">অনুপস্থিত</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-yellow-500"></span>
                <span class="text-gray-600 dark:text-gray-400">বিলম্বে</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-blue-500"></span>
                <span class="text-gray-600 dark:text-gray-400">ছুটি</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-purple-200 dark:bg-purple-800"></span>
                <span class="text-gray-600 dark:text-gray-400">শুক্রবার</span>
            </div>
        </div>

        {{-- Calendar Grid --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            {{-- Week Day Headers --}}
            <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-700">
                @foreach($calendarData['weekDays'] as $index => $day)
                    <div
                        class="p-3 text-center font-semibold text-sm {{ $index == 6 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }} border-b border-gray-200 dark:border-gray-600">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            {{-- Calendar Days --}}
            @foreach($calendarData['weeks'] as $week)
                <div class="grid grid-cols-7">
                    @foreach($week as $index => $day)
                        <div class="min-h-[80px] p-2 border-b border-r border-gray-100 dark:border-gray-700 last:border-r-0
                            {{ $day && $day['is_friday'] ? 'bg-purple-50 dark:bg-purple-900/20' : '' }}
                            {{ $day && $day['is_today'] ? 'bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-500 ring-inset' : '' }}
                        ">
                            @if($day)
                                <div class="flex items-start justify-between mb-1">
                                    <span
                                        class="text-sm font-medium {{ $day['is_today'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $day['day'] }}
                                    </span>

                                    @if($calendarData['is_student_view'] && $day['attendance'])
                                        <span class="w-3 h-3 rounded-full
                                                    @if($day['attendance'] === 'present') bg-green-500
                                                    @elseif($day['attendance'] === 'absent') bg-red-500
                                                    @elseif($day['attendance'] === 'late') bg-yellow-500
                                                    @elseif($day['attendance'] === 'leave') bg-blue-500
                                                    @else bg-gray-300 dark:bg-gray-600
                                                    @endif
                                                "></span>
                                    @endif
                                </div>

                                @if(!$calendarData['is_student_view'] && $day['summary'])
                                    {{-- Class summary view --}}
                                    <div class="text-xs space-y-0.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-green-600 dark:text-green-400">✓ {{ $day['summary']['present'] }}</span>
                                            <span class="text-red-600 dark:text-red-400">✗ {{ $day['summary']['absent'] }}</span>
                                        </div>
                                        <div class="text-center">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold
                                                        @if($day['summary']['percentage'] >= 80) bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300
                                                        @elseif($day['summary']['percentage'] >= 50) bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300
                                                        @else bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300
                                                        @endif
                                                    ">{{ $day['summary']['percentage'] }}%</span>
                                        </div>
                                    </div>
                                @elseif($calendarData['is_student_view'] && $day['attendance'])
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">
                                        @if($day['attendance'] === 'present') উপস্থিত
                                        @elseif($day['attendance'] === 'absent') অনুপস্থিত
                                        @elseif($day['attendance'] === 'late') বিলম্বে
                                        @elseif($day['attendance'] === 'leave') ছুটি
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <x-heroicon-o-calendar class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">ক্যালেন্ডার দেখুন</h3>
            <p class="text-gray-500 dark:text-gray-400">শ্রেণি নির্বাচন করে "ক্যালেন্ডার দেখুন" ক্লিক করুন।</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">নির্দিষ্ট ছাত্র নির্বাচন করলে তার ব্যক্তিগত ক্যালেন্ডার
                দেখতে পাবেন।</p>
        </div>
    @endif
</x-filament-panels::page>