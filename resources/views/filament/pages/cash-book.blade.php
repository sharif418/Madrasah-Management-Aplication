<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-book-open">
                ক্যাশ বুক দেখুন
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
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Opening Balance --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-gray-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">প্রারম্ভিক জের</p>
                        <p class="text-2xl font-bold {{ $summary['opening_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ৳{{ number_format($summary['opening_balance'], 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-full">
                        <x-heroicon-o-arrow-right-circle class="w-6 h-6 text-gray-600" />
                    </div>
                </div>
            </div>

            {{-- Total Credit --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">মোট জমা (Credit)</p>
                        <p class="text-2xl font-bold text-green-600">৳{{ number_format($summary['total_credit'], 0) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <x-heroicon-o-arrow-down-circle class="w-6 h-6 text-green-600" />
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    <span>ফি: ৳{{ number_format($summary['fee_collection'], 0) }}</span> |
                    <span>অন্যান্য: ৳{{ number_format($summary['other_income'], 0) }}</span>
                </div>
            </div>

            {{-- Total Debit --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">মোট খরচ (Debit)</p>
                        <p class="text-2xl font-bold text-red-600">৳{{ number_format($summary['total_debit'], 0) }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <x-heroicon-o-arrow-up-circle class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>

            {{-- Closing Balance --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">সমাপনী জের</p>
                        <p class="text-2xl font-bold {{ $summary['closing_balance'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            ৳{{ number_format($summary['closing_balance'], 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <x-heroicon-o-banknotes class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction Table --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5 text-blue-500" />
                    লেনদেন তালিকা ({{ $summary['transaction_count'] }} টি)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">তারিখ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">বিভাগ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">বিবরণ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">রেফারেন্স</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">জমা</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">খরচ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @php $runningBalance = $summary['opening_balance']; @endphp
                        
                        {{-- Opening Balance Row --}}
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <td class="px-4 py-3 font-medium" colspan="4">প্রারম্ভিক জের (Opening Balance)</td>
                            <td class="px-4 py-3 text-right font-bold text-blue-600" colspan="2">
                                ৳{{ number_format($summary['opening_balance'], 0) }}
                            </td>
                        </tr>

                        @forelse($transactions as $txn)
                            @php
                                if ($txn['type'] === 'credit') {
                                    $runningBalance += $txn['amount'];
                                } else {
                                    $runningBalance -= $txn['amount'];
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($txn['date'])->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $txn['type'] === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $txn['category'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ Str::limit($txn['description'], 40) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $txn['reference'] }}</td>
                                <td class="px-4 py-3 text-right {{ $txn['type'] === 'credit' ? 'text-green-600 font-semibold' : '' }}">
                                    {{ $txn['type'] === 'credit' ? '৳' . number_format($txn['amount'], 0) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right {{ $txn['type'] === 'debit' ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $txn['type'] === 'debit' ? '৳' . number_format($txn['amount'], 0) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <x-heroicon-o-document class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                    এই সময়ে কোন লেনদেন নেই
                                </td>
                            </tr>
                        @endforelse

                        {{-- Closing Balance Row --}}
                        <tr class="bg-blue-50 dark:bg-blue-900/20 font-bold">
                            <td class="px-4 py-3" colspan="4">সমাপনী জের (Closing Balance)</td>
                            <td class="px-4 py-3 text-right text-green-600">৳{{ number_format($summary['total_credit'], 0) }}</td>
                            <td class="px-4 py-3 text-right text-red-600">৳{{ number_format($summary['total_debit'], 0) }}</td>
                        </tr>
                        <tr class="bg-blue-100 dark:bg-blue-900/40 font-bold text-lg">
                            <td class="px-4 py-4" colspan="4">নেট ব্যালেন্স</td>
                            <td class="px-4 py-4 text-right {{ $summary['closing_balance'] >= 0 ? 'text-blue-600' : 'text-red-600' }}" colspan="2">
                                ৳{{ number_format($summary['closing_balance'], 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>
