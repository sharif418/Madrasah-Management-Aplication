<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form wire:submit="generate" class="space-y-4">
                {{ $this->form }}

                <div class="flex justify-end gap-3">
                    <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                        রিপোর্ট দেখুন
                    </x-filament::button>
                    @if($reportData)
                        <x-filament::button wire:click="downloadPdf" color="success" icon="heroicon-o-arrow-down-tray">
                            PDF ডাউনলোড
                        </x-filament::button>
                    @endif
                </div>
            </form>
        </div>

        @if($reportData)
            <!-- Summary Stats -->
            @php $stats = $this->getSummaryStats(); @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_students'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">মোট ছাত্র</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['avg_attendance'] }}%</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">গড় হাজিরা</div>
                </div>
                <div
                    class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-lg border border-emerald-200 dark:border-emerald-800">
                    <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['above_90'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">৯০% এর উপরে</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                    <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['below_50'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">৫০% এর নিচে</div>
                </div>
            </div>

            <!-- Report Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $reportData['class']->name ?? '' }} -
                            {{ $reportData['section'] }}</h3>
                        <p class="text-sm text-gray-500">
                            তারিখ: {{ \Carbon\Carbon::parse($reportData['start_date'])->format('d M Y') }}
                            থেকে {{ \Carbon\Carbon::parse($reportData['end_date'])->format('d M Y') }}
                            (মোট {{ $reportData['total_days'] }} দিন)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                রোল</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                নাম</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-green-600 uppercase tracking-wider">
                                উপস্থিত</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-red-600 uppercase tracking-wider">
                                অনুপস্থিত</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-yellow-600 uppercase tracking-wider">
                                বিলম্বে</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-blue-600 uppercase tracking-wider">
                                ছুটি</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                মোট দিন</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                শতাংশ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($reportData['students'] as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $item['student']->roll_no }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">{{ $item['student']->name }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-green-600 font-semibold">
                                            {{ $item['present'] }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-red-600 font-semibold">
                                            {{ $item['absent'] }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-yellow-600">{{ $item['late'] }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-blue-600">{{ $item['leave'] }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center">{{ $item['total'] }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center">
                                            <span
                                                class="px-2 py-1 rounded text-xs font-bold
                                                    {{ $item['percentage'] >= 90 ? 'bg-green-100 text-green-800' :
                            ($item['percentage'] >= 75 ? 'bg-blue-100 text-blue-800' :
                                ($item['percentage'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ $item['percentage'] }}%
                                            </span>
                                        </td>
                                    </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
                <x-heroicon-o-document-chart-bar class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">রিপোর্ট দেখতে ফিল্টার সিলেক্ট করুন</h3>
                <p class="text-sm text-gray-500 mt-2">ক্লাস, শাখা এবং তারিখ নির্বাচন করে "রিপোর্ট দেখুন" বাটনে ক্লিক করুন।
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>