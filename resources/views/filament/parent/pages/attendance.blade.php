<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            {{ $this->form }}
        </div>

        @php $attendance = $this->getAttendanceData(); @endphp

        @if(!empty($attendance['summary']))
            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div
                    class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-center border border-green-200 dark:border-green-800">
                    <div class="text-3xl font-bold text-green-600">{{ $attendance['summary']['present'] }}</div>
                    <div class="text-sm text-green-700 dark:text-green-400">উপস্থিত</div>
                </div>
                <div
                    class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 text-center border border-red-200 dark:border-red-800">
                    <div class="text-3xl font-bold text-red-600">{{ $attendance['summary']['absent'] }}</div>
                    <div class="text-sm text-red-700 dark:text-red-400">অনুপস্থিত</div>
                </div>
                <div
                    class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 text-center border border-yellow-200 dark:border-yellow-800">
                    <div class="text-3xl font-bold text-yellow-600">{{ $attendance['summary']['late'] }}</div>
                    <div class="text-sm text-yellow-700 dark:text-yellow-400">বিলম্বে</div>
                </div>
                <div
                    class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 text-center border border-blue-200 dark:border-blue-800">
                    <div class="text-3xl font-bold text-blue-600">{{ $attendance['summary']['leave'] }}</div>
                    <div class="text-sm text-blue-700 dark:text-blue-400">ছুটি</div>
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 text-center border border-gray-200 dark:border-gray-600">
                    <div class="text-3xl font-bold text-gray-600 dark:text-gray-300">{{ $attendance['summary']['total'] }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">মোট দিন</div>
                </div>
                <div
                    class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-4 text-center border border-teal-200 dark:border-teal-800">
                    <div class="text-3xl font-bold text-teal-600">{{ $attendance['summary']['percentage'] }}%</div>
                    <div class="text-sm text-teal-700 dark:text-teal-400">শতাংশ</div>
                </div>
            </div>

            <!-- Calendar View -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">ক্যালেন্ডার ভিউ</h3>

                <div class="grid grid-cols-7 gap-2 text-center">
                    <!-- Weekday Headers -->
                    @foreach(['রবি', 'সোম', 'মঙ্গল', 'বুধ', 'বৃহ', 'শুক্র', 'শনি'] as $day)
                        <div class="text-xs font-medium text-gray-500 py-2">{{ $day }}</div>
                    @endforeach

                    <!-- Empty cells for first week -->
                    @php
                        $startDayOfWeek = $attendance['start_date']->dayOfWeek;
                        $currentDate = $attendance['start_date']->copy();
                    @endphp

                    @for($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="p-2"></div>
                    @endfor

                    <!-- Calendar Days -->
                    @while($currentDate <= $attendance['end_date'])
                        @php
                            $dateKey = $currentDate->format('Y-m-d');
                            $record = $attendance['records'][$dateKey] ?? null;
                            $status = $record?->status;

                            $bgClass = match ($status) {
                                'present' => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300 border-green-300',
                                'absent' => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 border-red-300',
                                'late' => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300 border-yellow-300',
                                'leave' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 border-blue-300',
                                default => 'bg-gray-50 dark:bg-gray-700 text-gray-400 border-gray-200',
                            };

                            $isFriday = $currentDate->dayOfWeek === 5;
                        @endphp

                        <div class="p-2 rounded-lg border {{ $bgClass }} {{ $isFriday ? 'opacity-50' : '' }}"
                            title="{{ $status ?? 'No Data' }}">
                            <div class="text-lg font-medium">{{ $currentDate->day }}</div>
                            @if($status)
                                <div class="text-xs">
                                    {{ match ($status) { 'present' => '✓', 'absent' => '✗', 'late' => '⏰', 'leave' => '📋', default => ''} }}
                                </div>
                            @endif
                        </div>

                        @php $currentDate->addDay(); @endphp
                    @endwhile
                </div>

                <!-- Legend -->
                <div class="mt-6 flex flex-wrap gap-4 justify-center">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-green-100 border border-green-300"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">উপস্থিত</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-red-100 border border-red-300"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">অনুপস্থিত</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-yellow-100 border border-yellow-300"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">বিলম্বে</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded bg-blue-100 border border-blue-300"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">ছুটি</span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-12 text-center">
                <x-heroicon-o-calendar-days class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">সন্তান নির্বাচন করুন</h3>
                <p class="text-gray-500 mt-2">হাজিরা দেখতে উপরে থেকে সন্তান ও মাস নির্বাচন করুন।</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>