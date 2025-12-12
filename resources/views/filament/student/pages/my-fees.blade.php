<x-filament-panels::page>
    @php
        $summary = $this->getFeesSummary();
        $payments = $this->getPaymentHistory();
    @endphp

    <!-- Fee Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">মোট ফি</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">৳{{ number_format($summary['total']) }}</h3>
        </div>
        <div
            class="bg-success-50 dark:bg-success-900/20 rounded-xl p-4 shadow-sm border border-success-200 dark:border-success-800">
            <p class="text-sm text-success-600 dark:text-success-400">পরিশোধিত</p>
            <h3 class="text-2xl font-bold text-success-700 dark:text-success-300">৳{{ number_format($summary['paid']) }}
            </h3>
        </div>
        <div
            class="bg-danger-50 dark:bg-danger-900/20 rounded-xl p-4 shadow-sm border border-danger-200 dark:border-danger-800">
            <p class="text-sm text-danger-600 dark:text-danger-400">বকেয়া</p>
            <h3 class="text-2xl font-bold text-danger-700 dark:text-danger-300">৳{{ number_format($summary['due']) }}
            </h3>
        </div>
        <div
            class="bg-info-50 dark:bg-info-900/20 rounded-xl p-4 shadow-sm border border-info-200 dark:border-info-800">
            <p class="text-sm text-info-600 dark:text-info-400">ছাড়</p>
            <h3 class="text-2xl font-bold text-info-700 dark:text-info-300">৳{{ number_format($summary['discount']) }}
            </h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Fee Table -->
        <div
            class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">ফি বিবরণ</h3>
            {{ $this->table }}
        </div>

        <!-- Payment History -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-primary-500" />
                    সাম্প্রতিক পেমেন্ট
                </h3>
            </div>
            <div class="p-4">
                @if($payments->count() > 0)
                    <div class="space-y-3">
                        @foreach($payments as $payment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $payment->feeType?->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $payment->payment_date?->format('d M, Y') }}</p>
                                </div>
                                <span class="font-semibold text-success-600">৳{{ number_format($payment->amount) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">কোন পেমেন্ট রেকর্ড নেই</p>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>