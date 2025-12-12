<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            {{ $this->form }}
        </div>

        @php $data = $this->getFeeData(); @endphp

        @if(!empty($data['summary']))
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-blue-600 dark:text-blue-400">মোট ফি</div>
                            <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">৳{{ number_format($data['summary']['total_fees'], 0) }}</div>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-800 rounded-lg">
                            <x-heroicon-o-document-text class="w-6 h-6 text-blue-600 dark:text-blue-400"/>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-5 border border-green-200 dark:border-green-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-green-600 dark:text-green-400">পরিশোধিত</div>
                            <div class="text-2xl font-bold text-green-700 dark:text-green-300">৳{{ number_format($data['summary']['total_paid'], 0) }}</div>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-800 rounded-lg">
                            <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400"/>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-5 border border-red-200 dark:border-red-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-red-600 dark:text-red-400">বকেয়া</div>
                            <div class="text-2xl font-bold text-red-700 dark:text-red-300">৳{{ number_format($data['summary']['total_due'], 0) }}</div>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-800 rounded-lg">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400"/>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-5 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">পেন্ডিং ফি</div>
                            <div class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $data['summary']['pending_count'] }} টি</div>
                        </div>
                        <div class="p-3 bg-gray-100 dark:bg-gray-600 rounded-lg">
                            <x-heroicon-o-clock class="w-6 h-6 text-gray-600 dark:text-gray-400"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pending Fees -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-document-text class="w-5 h-5 text-teal-500"/>
                            ফি তালিকা
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                        @forelse($data['fees'] as $fee)
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $fee->feeType->name ?? 'N/A' }}</h4>
                                        <p class="text-sm text-gray-500">{{ $fee->academicYear->name ?? '' }}</p>
                                        @if($fee->due_date)
                                            <p class="text-xs text-gray-400 mt-1">
                                                নির্ধারিত তারিখ: {{ $fee->due_date->format('d M Y') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900 dark:text-white">৳{{ number_format($fee->amount, 0) }}</div>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $fee->status === 'paid' ? 'bg-green-100 text-green-700' : 
                                               ($fee->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ match($fee->status) {
                                                'paid' => 'পরিশোধিত',
                                                'partial' => 'আংশিক',
                                                default => 'বকেয়া',
                                            } }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">কোন ফি নেই</div>
                        @endforelse
                    </div>
                </div>

                <!-- Payment History -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-banknotes class="w-5 h-5 text-teal-500"/>
                            পেমেন্ট হিস্ট্রি
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                        @forelse($data['payments'] as $payment)
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            {{ $payment->studentFee?->feeType?->name ?? 'ফি পেমেন্ট' }}
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            {{ $payment->payment_date?->format('d M Y') ?? 'N/A' }}
                                        </p>
                                        @if($payment->receipt_no)
                                            <p class="text-xs text-gray-400">রসিদ: {{ $payment->receipt_no }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-green-600">৳{{ number_format($payment->amount, 0) }}</div>
                                        <span class="text-xs text-gray-500">
                                            {{ match($payment->payment_method ?? 'cash') {
                                                'bank' => 'ব্যাংক',
                                                'bkash' => 'বিকাশ',
                                                default => 'নগদ',
                                            } }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">কোন পেমেন্ট রেকর্ড নেই</div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($data['summary']['total_due'] > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4 text-center">
                    <p class="text-yellow-700 dark:text-yellow-400">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 inline-block mr-1"/>
                        বকেয়া পরিশোধ করতে অনুগ্রহ করে মাদরাসা অফিসে যোগাযোগ করুন।
                    </p>
                </div>
            @endif
        @else
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-12 text-center">
                <x-heroicon-o-banknotes class="w-16 h-16 mx-auto mb-4 text-gray-400"/>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">সন্তান নির্বাচন করুন</h3>
                <p class="text-gray-500 mt-2">ফি তথ্য দেখতে উপরে থেকে সন্তান নির্বাচন করুন।</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
