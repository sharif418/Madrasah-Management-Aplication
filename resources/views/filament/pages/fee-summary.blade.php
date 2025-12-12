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
        {{-- Overall Summary Cards --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Total Assigned --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">মোট নির্ধারিত</p>
                        <p class="text-2xl font-bold text-blue-600">৳{{ number_format($summary['total_assigned'], 0) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <x-heroicon-o-calculator class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Total Collected --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">মোট আদায়</p>
                        <p class="text-2xl font-bold text-green-600">৳{{ number_format($summary['total_collected'], 0) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <x-heroicon-o-banknotes class="w-6 h-6 text-green-600" />
                    </div>
                </div>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $summary['collection_rate'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $summary['collection_rate'] }}% আদায়</p>
                </div>
            </div>

            {{-- Total Due --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">মোট বকেয়া</p>
                        <p class="text-2xl font-bold text-red-600">৳{{ number_format($summary['total_due'], 0) }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600" />
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $summary['due_students'] }} জন ছাত্র বকেয়া</p>
            </div>

            {{-- Students --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">মোট ছাত্র</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $summary['total_students'] }} জন</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <x-heroicon-o-users class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $summary['paid_students'] }} জন পরিশোধ সম্পন্ন</p>
            </div>
        </div>

        {{-- Class-wise and Fee Type Summary --}}
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Class-wise Summary --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <x-heroicon-o-academic-cap class="w-5 h-5 text-blue-500" />
                    শ্রেণিভিত্তিক সামারি
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-2 px-2">শ্রেণি</th>
                                <th class="text-right py-2 px-2">নির্ধারিত</th>
                                <th class="text-right py-2 px-2">আদায়</th>
                                <th class="text-right py-2 px-2">বকেয়া</th>
                                <th class="text-center py-2 px-2">হার</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classWise as $item)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="py-2 px-2 font-medium">{{ $item['class_name'] }}</td>
                                    <td class="text-right py-2 px-2">৳{{ number_format($item['total'], 0) }}</td>
                                    <td class="text-right py-2 px-2 text-green-600">৳{{ number_format($item['collected'], 0) }}</td>
                                    <td class="text-right py-2 px-2 text-red-600">৳{{ number_format($item['due'], 0) }}</td>
                                    <td class="text-center py-2 px-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item['rate'] >= 80 ? 'bg-green-100 text-green-800' : ($item['rate'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $item['rate'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">কোন ডাটা নেই</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Fee Type Summary --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <x-heroicon-o-tag class="w-5 h-5 text-green-500" />
                    ফি টাইপ অনুযায়ী সামারি
                </h3>
                <div class="space-y-4">
                    @forelse($feeTypeWise as $item)
                        <div class="border-b dark:border-gray-700 pb-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">{{ $item['fee_type'] }}</span>
                                <span class="text-sm text-gray-500">{{ $item['rate'] }}% আদায়</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500" style="width: {{ $item['rate'] }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1 text-xs text-gray-500">
                                <span>আদায়: ৳{{ number_format($item['collected'], 0) }}</span>
                                <span>মোট: ৳{{ number_format($item['total'], 0) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">কোন ডাটা নেই</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Monthly Trend --}}
        @if(count($monthlyTrend) > 0)
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="w-5 h-5 text-purple-500" />
                    মাসিক আদায় ট্রেন্ড
                </h3>
                <div class="flex items-end gap-2 h-48">
                    @php
                        $maxTotal = max(array_column($monthlyTrend, 'total'));
                    @endphp
                    @foreach($monthlyTrend as $item)
                        @php
                            $height = $maxTotal > 0 ? ($item['total'] / $maxTotal) * 100 : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-gradient-to-t from-blue-500 to-blue-300 rounded-t-lg transition-all duration-500 hover:from-blue-600 hover:to-blue-400" style="height: {{ $height }}%"></div>
                            <p class="text-xs mt-2 text-gray-500">{{ $item['month'] }}</p>
                            <p class="text-xs font-semibold">৳{{ number_format($item['total'] / 1000, 0) }}K</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</x-filament-panels::page>
