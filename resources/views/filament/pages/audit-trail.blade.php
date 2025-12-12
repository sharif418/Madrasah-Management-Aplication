<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-clipboard-document-list">
                অডিট দেখুন
            </x-filament::button>
        </div>
    </form>

    @if($showReport)
        {{-- Summary --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-lg font-bold">মোট লেনদেন:</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $logs->count() }} টি</span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">মোট জমা</p>
                        <p class="text-xl font-bold text-green-600">
                            ৳{{ number_format($logs->where('is_credit', true)->sum('amount'), 0) }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">মোট খরচ</p>
                        <p class="text-xl font-bold text-red-600">
                            ৳{{ number_format($logs->where('is_credit', false)->sum('amount'), 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Audit Log Table --}}
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold">তারিখ/সময়</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">ধরণ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">রেফারেন্স</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">বিবরণ</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold">পরিমাণ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">ব্যবহারকারী</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($logs as $log)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-3 text-sm">
                                                    {{ \Carbon\Carbon::parse($log['datetime'])->format('d M Y, h:i A') }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $typeColors = [
                                                            'fee_payment' => 'bg-green-100 text-green-800',
                                                            'income' => 'bg-emerald-100 text-emerald-800',
                                                            'expense' => 'bg-red-100 text-red-800',
                                                            'salary' => 'bg-orange-100 text-orange-800',
                                                            'advance' => 'bg-yellow-100 text-yellow-800',
                                                            'loan' => 'bg-purple-100 text-purple-800',
                                                            'refund' => 'bg-pink-100 text-pink-800',
                                                        ];
                                                    @endphp
                             <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $typeColors[$log['type_key']] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ $log['type'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-mono bg-gray-100 text-gray-600">
                                                        {{ $log['reference'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">{{ Str::limit($log['description'], 40) }}</td>
                                                <td
                                                    class="px-4 py-3 text-right font-semibold {{ $log['is_credit'] ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $log['is_credit'] ? '+' : '-' }}৳{{ number_format($log['amount'], 0) }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $log['user'] }}</td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <x-heroicon-o-clipboard-document class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                    এই সময়ে কোন লেনদেন নেই
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>