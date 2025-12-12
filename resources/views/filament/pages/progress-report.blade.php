<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        <form wire:submit="generateReport">
            {{ $this->form }}

            <div class="mt-4 flex gap-3">
                <x-filament::button type="submit" icon="heroicon-o-arrow-trending-up">
                    প্রতিবেদন তৈরি
                </x-filament::button>

                @if($progressData)
                    <x-filament::button type="button" wire:click="downloadPdf" color="success"
                        icon="heroicon-o-arrow-down-tray">
                        PDF ডাউনলোড
                    </x-filament::button>
                @endif
            </div>
        </form>
    </div>

    @if($showReport && $progressData)
        {{-- Student Info --}}
        <div
            class="mb-6 p-4 bg-gradient-to-r from-primary-50 to-green-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-primary-200 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-400 text-2xl font-bold">
                    {{ mb_substr($progressData['student']->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                        {{ $progressData['student']->name }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $progressData['student']->class?->name ?? '' }} -
                        {{ $progressData['student']->section?->name ?? '' }} |
                        রোল: {{ $progressData['student']->roll_no ?? '-' }} |
                        {{ $progressData['academic_year']->name ?? '' }}
                    </p>
                </div>
                <div class="flex gap-4">
                    <div class="text-center px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <span
                            class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $progressData['summary']['avg_gpa'] }}</span>
                        <span class="text-xs block text-gray-500 dark:text-gray-400">গড় GPA</span>
                    </div>
                    <div class="text-center px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <span
                            class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $progressData['summary']['passed_count'] }}/{{ $progressData['summary']['total_exams'] }}</span>
                        <span class="text-xs block text-gray-500 dark:text-gray-400">পাস</span>
                    </div>
                    <div
                        class="text-center px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm flex items-center gap-2">
                        @if($progressData['summary']['trend'] === 'improving')
                            <x-heroicon-o-arrow-trending-up class="w-6 h-6 text-green-500" />
                            <span class="text-sm text-green-600 dark:text-green-400">উন্নতি</span>
                        @elseif($progressData['summary']['trend'] === 'declining')
                            <x-heroicon-o-arrow-trending-down class="w-6 h-6 text-red-500" />
                            <span class="text-sm text-red-600 dark:text-red-400">অবনতি</span>
                        @else
                            <x-heroicon-o-minus class="w-6 h-6 text-gray-500" />
                            <span class="text-sm text-gray-600 dark:text-gray-400">স্থিতিশীল</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Timeline --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">📊 পরীক্ষাভিত্তিক ফলাফল</h4>

            <div class="relative">
                {{-- Timeline Line --}}
                <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

                <div class="space-y-6">
                    @foreach($progressData['exams'] as $index => $examData)
                        <div class="relative flex gap-4">
                            {{-- Timeline Dot --}}
                            <div
                                class="relative z-10 w-16 h-16 rounded-full flex items-center justify-center text-white font-bold
                                {{ $examData['result_status'] === 'passed' ? 'bg-green-500' : ($examData['result_status'] === 'failed' ? 'bg-red-500' : 'bg-gray-400') }}">
                                {{ $index + 1 }}
                            </div>

                            {{-- Exam Card --}}
                            <div
                                class="flex-1 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h5 class="font-semibold text-gray-800 dark:text-white">{{ $examData['exam']->name }}
                                        </h5>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $examData['exam']->start_date ? $examData['exam']->start_date->format('M Y') : '-' }}
                                        </p>
                                    </div>
                                    <span
                                        class="px-3 py-1 rounded-full text-sm font-semibold
                                        {{ $examData['result_status'] === 'passed' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : ($examData['result_status'] === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300') }}">
                                        {{ $examData['result_status'] === 'passed' ? 'উত্তীর্ণ' : ($examData['result_status'] === 'failed' ? 'অনুত্তীর্ণ' : 'N/A') }}
                                    </span>
                                </div>

                                @if($examData['result'])
                                    <div class="mt-3 grid grid-cols-5 gap-3">
                                        <div class="p-2 bg-white dark:bg-gray-800 rounded text-center">
                                            <div class="text-lg font-bold text-gray-700 dark:text-gray-200">
                                                {{ $examData['total_marks'] }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">প্রাপ্ত</div>
                                        </div>
                                        <div class="p-2 bg-white dark:bg-gray-800 rounded text-center">
                                            <div class="text-lg font-bold text-gray-700 dark:text-gray-200">
                                                {{ $examData['full_marks'] }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">পূর্ণ</div>
                                        </div>
                                        <div class="p-2 bg-white dark:bg-gray-800 rounded text-center">
                                            <div class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                                {{ number_format($examData['percentage'], 1) }}%</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">শতাংশ</div>
                                        </div>
                                        <div class="p-2 bg-white dark:bg-gray-800 rounded text-center">
                                            <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                {{ number_format($examData['gpa'], 2) }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">GPA</div>
                                        </div>
                                        <div class="p-2 bg-white dark:bg-gray-800 rounded text-center">
                                            <div class="text-lg font-bold text-yellow-600 dark:text-yellow-400">
                                                {{ $examData['position'] }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">মেধা ক্রম</div>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="mt-3 p-3 bg-gray-100 dark:bg-gray-600 rounded text-center text-gray-500 dark:text-gray-400">
                                        ফলাফল পাওয়া যায়নি
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <x-heroicon-o-arrow-trending-up class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">অগ্রগতি প্রতিবেদন</h3>
            <p class="text-gray-500 dark:text-gray-400">শিক্ষাবর্ষ ও ছাত্র নির্বাচন করে "প্রতিবেদন তৈরি" ক্লিক করুন।</p>
        </div>
    @endif
</x-filament-panels::page>