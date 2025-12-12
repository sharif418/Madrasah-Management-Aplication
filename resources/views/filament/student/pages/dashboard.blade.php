<x-filament-panels::page>
    @php
        $student = $this->getStudent();
        $attendanceStats = $this->getAttendanceStats();
        $feeStatus = $this->getFeeStatus();
        $recentResults = $this->getRecentResults();
        $recentAttendance = $this->getRecentAttendance();
    @endphp

    @if(!$student)
        <div class="p-6 text-center bg-danger-50 dark:bg-danger-900/20 rounded-xl">
            <x-heroicon-o-exclamation-triangle class="w-16 h-16 mx-auto text-danger-500 mb-4" />
            <h3 class="text-lg font-semibold text-danger-700 dark:text-danger-400">ছাত্র প্রোফাইল পাওয়া যায়নি</h3>
            <p class="text-danger-600 dark:text-danger-400">আপনার একাউন্টের সাথে কোন ছাত্র প্রোফাইল সংযুক্ত নেই।</p>
        </div>
    @else
        <!-- Welcome Section -->
        <div class="p-6 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl text-white mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                    @if($student->photo_url)
                        <img src="{{ $student->photo_url }}" alt="{{ $student->name }}"
                            class="w-14 h-14 rounded-full object-cover">
                    @else
                        <x-heroicon-o-user class="w-8 h-8" />
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold">আস্সালামু আলাইকুম, {{ $student->name }}!</h2>
                    <p class="text-primary-100">{{ $student->class?->name }} | রোল: {{ $student->roll_no ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Attendance Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">এই মাসের উপস্থিতি</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceStats['percentage'] }}%
                        </h3>
                    </div>
                    <div
                        class="w-12 h-12 bg-success-100 dark:bg-success-900/30 rounded-full flex items-center justify-center">
                        <x-heroicon-o-clipboard-document-check class="w-6 h-6 text-success-600 dark:text-success-400" />
                    </div>
                </div>
                <div class="mt-2 flex gap-2 text-sm">
                    <span class="text-success-600">{{ $attendanceStats['present'] }} উপস্থিত</span>
                    <span class="text-danger-600">{{ $attendanceStats['absent'] }} অনুপস্থিত</span>
                </div>
            </div>

            <!-- Fee Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">বকেয়া ফি</p>
                        <h3 class="text-2xl font-bold {{ $feeStatus['due'] > 0 ? 'text-danger-600' : 'text-success-600' }}">
                            ৳{{ number_format($feeStatus['due']) }}
                        </h3>
                    </div>
                    <div
                        class="w-12 h-12 bg-warning-100 dark:bg-warning-900/30 rounded-full flex items-center justify-center">
                        <x-heroicon-o-banknotes class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    মোট: ৳{{ number_format($feeStatus['total']) }} | পরিশোধিত: ৳{{ number_format($feeStatus['paid']) }}
                </div>
            </div>

            <!-- Class Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">শ্রেণি</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $student->class?->name ?? 'N/A' }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-info-100 dark:bg-info-900/30 rounded-full flex items-center justify-center">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-info-600 dark:text-info-400" />
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    শাখা: {{ $student->section?->name ?? 'N/A' }}
                </div>
            </div>

            <!-- Admission Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">ভর্তি নম্বর</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $student->admission_no }}</h3>
                    </div>
                    <div
                        class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                        <x-heroicon-o-identification class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    ভর্তির তারিখ: {{ $student->admission_date?->format('d M, Y') }}
                </div>
            </div>
        </div>

        <!-- Recent Activity Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Results -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-document-chart-bar class="w-5 h-5 text-primary-500" />
                        সাম্প্রতিক ফলাফল
                    </h3>
                </div>
                <div class="p-4">
                    @if($recentResults->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentResults as $result)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $result->exam?->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $result->exam?->examType?->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $result->status === 'pass' ? 'bg-success-100 text-success-800' : 'bg-danger-100 text-danger-800' }}">
                                            {{ $result->grade }} ({{ $result->percentage }}%)
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">কোন ফলাফল নেই</p>
                    @endif
                </div>
            </div>

            <!-- Recent Attendance -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-primary-500" />
                        সাম্প্রতিক উপস্থিতি
                    </h3>
                </div>
                <div class="p-4">
                    @if($recentAttendance->count() > 0)
                        <div class="space-y-2">
                            @foreach($recentAttendance as $attendance)
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $attendance->date->format('d M, Y') }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($attendance->status === 'present') bg-success-100 text-success-800
                                                    @elseif($attendance->status === 'absent') bg-danger-100 text-danger-800
                                                    @elseif($attendance->status === 'late') bg-warning-100 text-warning-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                        {{ $attendance->status === 'present' ? 'উপস্থিত' : ($attendance->status === 'absent' ? 'অনুপস্থিত' : ($attendance->status === 'late' ? 'বিলম্বে' : 'ছুটি')) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">কোন উপস্থিতি রেকর্ড নেই</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>