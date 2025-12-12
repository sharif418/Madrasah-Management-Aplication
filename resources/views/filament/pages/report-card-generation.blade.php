<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        <form wire:submit="generate">
            {{ $this->form }}

            <div class="mt-4 flex gap-3">
                <x-filament::button type="submit" icon="heroicon-o-document-text">
                    রিপোর্ট কার্ড তৈরি
                </x-filament::button>

                @if($reportData)
                    <x-filament::button type="button" wire:click="downloadPdf" color="success"
                        icon="heroicon-o-arrow-down-tray">
                        PDF ডাউনলোড
                    </x-filament::button>
                @endif
            </div>
        </form>
    </div>

    @if($showPreview && $reportData)
        {{-- Preview Section --}}
        <div class="space-y-6">
            {{-- Summary Header --}}
            <div
                class="p-4 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-primary-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            {{ $reportData['exam']->name ?? '' }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $reportData['class']->name ?? '' }}
                            @if($reportData['section'])
                                - {{ $reportData['section'] }}
                            @endif
                            | {{ $reportData['exam']->academicYear?->name ?? '' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            {{ count($reportData['cards']) }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 block">জন ছাত্র</span>
                    </div>
                </div>
            </div>

            {{-- Report Cards Preview --}}
            @foreach($reportData['cards'] as $card)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    {{-- Card Header --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <div class="flex items-center gap-4">
                            @if($card['include_photo'] && $card['student']->photo)
                                <img src="{{ asset('storage/' . $card['student']->photo) }}"
                                    class="w-16 h-16 rounded-full object-cover border-2 border-primary-500"
                                    alt="{{ $card['student']->name }}">
                            @else
                                <div
                                    class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-400 text-xl font-bold">
                                    {{ mb_substr($card['student']->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                                    {{ $card['student']->name }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    রোল: {{ $card['student']->roll_no ?? '-' }} |
                                    আইডি: {{ $card['student']->student_id ?? $card['student']->admission_no }}
                                </p>
                            </div>
                            @if($card['result'])
                                <div class="text-right">
                                    <div
                                        class="text-2xl font-bold {{ $card['result']->result_status === 'passed' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        GPA {{ number_format($card['result']->gpa ?? 0, 2) }}
                                    </div>
                                    <div class="text-sm">
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs font-semibold
                                            {{ $card['result']->result_status === 'passed' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' }}">
                                            {{ $card['result']->result_status === 'passed' ? 'উত্তীর্ণ' : 'অনুত্তীর্ণ' }}
                                        </span>
                                        @if($card['result']->position)
                                            <span class="ml-2 text-gray-500 dark:text-gray-400">
                                                Position: {{ $card['result']->position }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Subject Table --}}
                    <div class="p-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-2 font-medium text-gray-700 dark:text-gray-300">বিষয়</th>
                                    <th class="text-center py-2 font-medium text-gray-700 dark:text-gray-300">পূর্ণ নম্বর</th>
                                    <th class="text-center py-2 font-medium text-gray-700 dark:text-gray-300">প্রাপ্ত নম্বর</th>
                                    <th class="text-center py-2 font-medium text-gray-700 dark:text-gray-300">গ্রেড</th>
                                    <th class="text-center py-2 font-medium text-gray-700 dark:text-gray-300">স্ট্যাটাস</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($card['subjects'] as $subject)
                                    <tr>
                                        <td class="py-2 text-gray-800 dark:text-gray-200">{{ $subject['subject'] }}</td>
                                        <td class="py-2 text-center text-gray-600 dark:text-gray-400">{{ $subject['full_marks'] }}
                                        </td>
                                        <td
                                            class="py-2 text-center font-semibold {{ $subject['is_absent'] ? 'text-red-500' : 'text-gray-800 dark:text-gray-200' }}">
                                            {{ $subject['is_absent'] ? 'অনুপস্থিত' : $subject['obtained'] }}
                                        </td>
                                        <td class="py-2 text-center">
                                            <span
                                                class="px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                                {{ $subject['grade'] }}
                                            </span>
                                        </td>
                                        <td class="py-2 text-center">
                                            @if($subject['is_absent'])
                                                <span class="text-red-500">-</span>
                                            @elseif($subject['is_passed'])
                                                <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 inline" />
                                            @else
                                                <x-heroicon-o-x-circle class="w-5 h-5 text-red-500 inline" />
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500 dark:text-gray-400">
                                            কোন বিষয় পাওয়া যায়নি
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Attendance Section --}}
                    @if($card['attendance'])
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">উপস্থিতি সামারি</h5>
                            <div class="flex gap-4 text-sm">
                                <span class="text-green-600 dark:text-green-400">
                                    উপস্থিত: {{ $card['attendance']['present'] }}
                                </span>
                                <span class="text-red-600 dark:text-red-400">
                                    অনুপস্থিত: {{ $card['attendance']['absent'] }}
                                </span>
                                <span class="text-yellow-600 dark:text-yellow-400">
                                    বিলম্বে: {{ $card['attendance']['late'] }}
                                </span>
                                <span class="text-primary-600 dark:text-primary-400 font-semibold">
                                    {{ $card['attendance']['percentage'] }}%
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <x-heroicon-o-document-chart-bar class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">রিপোর্ট কার্ড তৈরি করুন</h3>
            <p class="text-gray-500 dark:text-gray-400">পরীক্ষা ও শ্রেণি নির্বাচন করে "রিপোর্ট কার্ড তৈরি" ক্লিক করুন।</p>
        </div>
    @endif
</x-filament-panels::page>