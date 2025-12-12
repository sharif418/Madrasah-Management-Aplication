<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        <form wire:submit="generate">
            {{ $this->form }}

            <div class="mt-4 flex gap-3">
                <x-filament::button type="submit" icon="heroicon-o-funnel">
                    সামারি দেখুন
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

    @if($reportData)
        {{-- Summary Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div
                class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-center shadow-sm">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ count($reportData['students']) }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">মোট ছাত্র</div>
            </div>
            <div
                class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-center shadow-sm">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                    {{ $reportData['overall_stats']['percentage'] }}%</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">গড় উপস্থিতি</div>
            </div>
            <div
                class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-center shadow-sm">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $reportData['total_working_days'] }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">কার্যদিবস</div>
            </div>
            <div
                class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-center shadow-sm">
                <div class="text-3xl font-bold text-red-600 dark:text-red-400">
                    {{ $reportData['overall_stats']['total_absent'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">মোট অনুপস্থিতি</div>
            </div>
            <div
                class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-center shadow-sm">
                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                    {{ $reportData['overall_stats']['total_late'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">মোট বিলম্বে</div>
            </div>
        </div>

        {{-- Header Info --}}
        <div
            class="mb-4 p-4 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-primary-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ $reportData['class']->name ?? '' }}
                @if($reportData['section'] !== 'সকল শাখা')
                    - {{ $reportData['section'] }}
                @endif
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $reportData['month_name'] }} {{ $reportData['year'] }} |
                কার্যদিবস: {{ $reportData['total_working_days'] }} দিন
            </p>
        </div>

        {{-- Legend --}}
        <div class="mb-4 flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-green-500"></span>
                <span class="text-gray-600 dark:text-gray-400">উপস্থিত</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-red-500"></span>
                <span class="text-gray-600 dark:text-gray-400">অনুপস্থিত</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-yellow-500"></span>
                <span class="text-gray-600 dark:text-gray-400">বিলম্বে</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-blue-500"></span>
                <span class="text-gray-600 dark:text-gray-400">ছুটি</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-gray-300 dark:bg-gray-600"></span>
                <span class="text-gray-600 dark:text-gray-400">হাজিরা নেই</span>
            </div>
        </div>

        {{-- Student Table --}}
        <div
            class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-3 py-3 text-left font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap sticky left-0 bg-gray-50 dark:bg-gray-700 z-10">
                            ক্রম</th>
                        <th
                            class="px-3 py-3 text-left font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap sticky left-12 bg-gray-50 dark:bg-gray-700 z-10">
                            ছাত্রের নাম</th>
                        <th
                            class="px-2 py-3 text-center font-medium text-gray-700 dark:text-gray-300 bg-green-50 dark:bg-green-900/30">
                            উপ.</th>
                        <th
                            class="px-2 py-3 text-center font-medium text-gray-700 dark:text-gray-300 bg-red-50 dark:bg-red-900/30">
                            অনু.</th>
                        <th
                            class="px-2 py-3 text-center font-medium text-gray-700 dark:text-gray-300 bg-yellow-50 dark:bg-yellow-900/30">
                            বি.</th>
                        <th
                            class="px-2 py-3 text-center font-medium text-gray-700 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/30">
                            ছু.</th>
                        <th class="px-3 py-3 text-center font-medium text-gray-700 dark:text-gray-300">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($reportData['students'] as $index => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400 sticky left-0 bg-white dark:bg-gray-800">
                                {{ $index + 1 }}</td>
                            <td
                                class="px-3 py-2 font-medium text-gray-800 dark:text-gray-200 sticky left-12 bg-white dark:bg-gray-800">
                                <div>{{ $item['student']->name }}</div>
                                <div class="text-xs text-gray-500">রোল: {{ $item['student']->roll_no ?? '-' }}</div>
                            </td>
                            <td
                                class="px-2 py-2 text-center text-green-600 dark:text-green-400 font-semibold bg-green-50/50 dark:bg-green-900/20">
                                {{ $item['present'] }}</td>
                            <td
                                class="px-2 py-2 text-center text-red-600 dark:text-red-400 font-semibold bg-red-50/50 dark:bg-red-900/20">
                                {{ $item['absent'] }}</td>
                            <td
                                class="px-2 py-2 text-center text-yellow-600 dark:text-yellow-400 font-semibold bg-yellow-50/50 dark:bg-yellow-900/20">
                                {{ $item['late'] }}</td>
                            <td
                                class="px-2 py-2 text-center text-blue-600 dark:text-blue-400 font-semibold bg-blue-50/50 dark:bg-blue-900/20">
                                {{ $item['leave'] }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                    @if($item['percentage'] >= 90) bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300
                                    @elseif($item['percentage'] >= 75) bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                                    @elseif($item['percentage'] >= 50) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300
                                    @else bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                                    @endif
                                ">
                                    {{ $item['percentage'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <x-heroicon-o-clipboard-document-list class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">মাসিক সামারি দেখুন</h3>
            <p class="text-gray-500 dark:text-gray-400">উপরের ফিল্টার থেকে শ্রেণি ও মাস নির্বাচন করে "সামারি দেখুন" ক্লিক
                করুন।</p>
        </div>
    @endif
</x-filament-panels::page>