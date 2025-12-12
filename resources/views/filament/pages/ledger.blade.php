<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-document-text">
                খতিয়ান দেখুন
            </x-filament::button>

            @if($showReport)
                <x-filament::button color="success" wire:click="exportPdf" icon="heroicon-o-document-arrow-down">
                    PDF ডাউনলোড
                </x-filament::button>
            @endif
        </div>
    </form>

    @if($showReport)
        {{-- Header --}}
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-xl font-bold">{{ $headName }}</h3>
                    <p class="text-sm text-gray-500">{{ $data['type'] === 'income' ? 'আয়ের খতিয়ান' : 'ব্যয়ের খতিয়ান' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">মোট এন্ট্রি: <span class="font-bold">{{ $summary['count'] }} টি</span>
                    </p>
                </div>
            </div>

            {{-- Entries Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold">তারিখ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">বিবরণ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">ভাউচার</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold">ডেবিট</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold">ক্রেডিট</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @php $runningBalance = 0; @endphp
                        @forelse($entries as $entry)
                            @php
                                $runningBalance += $entry['credit'] - $entry['debit'];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($entry['date'])->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ $entry['description'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $entry['voucher'] }}</td>
                                <td class="px-4 py-3 text-right {{ $entry['debit'] > 0 ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $entry['debit'] > 0 ? '৳' . number_format($entry['debit'], 0) : '-' }}
                                </td>
                                <td
                                    class="px-4 py-3 text-right {{ $entry['credit'] > 0 ? 'text-green-600 font-semibold' : '' }}">
                                    {{ $entry['credit'] > 0 ? '৳' . number_format($entry['credit'], 0) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <x-heroicon-o-document class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                    এই সময়ে কোন লেনদেন নেই
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-100 dark:bg-gray-700 font-bold">
                        <tr>
                            <td class="px-4 py-4" colspan="3">মোট</td>
                            <td class="px-4 py-4 text-right text-red-600">৳{{ number_format($summary['total_debit'], 0) }}
                            </td>
                            <td class="px-4 py-4 text-right text-green-600">
                                ৳{{ number_format($summary['total_credit'], 0) }}</td>
                        </tr>
                        <tr class="text-lg">
                            <td class="px-4 py-4" colspan="3">ব্যালেন্স</td>
                            <td class="px-4 py-4 text-right {{ $summary['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}"
                                colspan="2">
                                ৳{{ number_format(abs($summary['balance']), 0) }}
                                {{ $summary['balance'] >= 0 ? 'Cr' : 'Dr' }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>