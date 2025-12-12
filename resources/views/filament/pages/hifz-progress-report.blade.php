<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-chart-bar">
                রিপোর্ট দেখুন
            </x-filament::button>

            @if($showReport)
                <x-filament::button color="success" wire:click="exportPdf" icon="heroicon-o-document-arrow-down">
                    PDF ডাউনলোড
                </x-filament::button>
            @endif
        </div>
    </form>

    @if($showReport)
        {{-- Summary Cards --}}
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-users class="w-8 h-8 mx-auto text-blue-500" />
                <p class="text-2xl font-bold text-blue-600 mt-2">{{ $summary['total_students'] }}</p>
                <p class="text-sm text-gray-500">মোট ছাত্র</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-book-open class="w-8 h-8 mx-auto text-green-500" />
                <p class="text-2xl font-bold text-green-600 mt-2">{{ $summary['avg_para'] }}</p>
                <p class="text-sm text-gray-500">গড় পারা</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-document-text class="w-8 h-8 mx-auto text-purple-500" />
                <p class="text-2xl font-bold text-purple-600 mt-2">{{ $summary['total_lines'] }}</p>
                <p class="text-sm text-gray-500">মোট লাইন</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-check-badge class="w-8 h-8 mx-auto text-emerald-500" />
                <p class="text-2xl font-bold text-emerald-600 mt-2">{{ $summary['completed_hifz'] }}</p>
                <p class="text-sm text-gray-500">হিফজ সম্পন্ন</p>
            </div>
        </div>

        {{-- Student Table --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <x-heroicon-o-academic-cap class="w-5 h-5 text-green-500" />
                    ছাত্র প্রগ্রেস
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold">রোল</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">নাম</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">বর্তমান পারা</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">সম্পন্ন পারা</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">সাবাক দিন</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">মোট লাইন</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">গড় মান</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">অগ্রগতি</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $student)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-3 font-mono text-sm">{{ $student['roll'] ?? '-' }}</td>
                                                <td class="px-4 py-3 font-medium">{{ $student['name'] }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        পারা {{ $student['current_para'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center font-semibold text-green-600">
                                                    {{ $student['completed_paras'] }}/30</td>
                                                <td class="px-4 py-3 text-center">{{ $student['total_sabaq_days'] }}</td>
                                                <td class="px-4 py-3 text-center text-purple-600">{{ $student['total_lines'] }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @php
                                                        $qualityColor = match ($student['avg_quality']) {
                                                            'অতি উত্তম' => 'bg-green-100 text-green-800',
                                                            'উত্তম' => 'bg-blue-100 text-blue-800',
                                                            'মধ্যম' => 'bg-yellow-100 text-yellow-800',
                                                            'দুর্বল' => 'bg-red-100 text-red-800',
                                                            default => 'bg-gray-100 text-gray-800',
                                                        };
                                                    @endphp
                             <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $qualityColor }}">
                                                        {{ $student['avg_quality'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-green-500 h-2 rounded-full"
                                                            style="width: {{ $student['progress_percentage'] }}%"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-500 mt-1">{{ $student['progress_percentage'] }}%</span>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    <x-heroicon-o-book-open class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                    কোন ছাত্র পাওয়া যায়নি
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>