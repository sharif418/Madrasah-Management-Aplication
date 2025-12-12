<x-filament-panels::page>
    <form wire:submit="loadStudents">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                অনুসন্ধান করুন
            </x-filament::button>
        </div>
    </form>

    @if($showStudents)
        {{-- Summary Bar --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">মোট বকেয়া</p>
                        <p class="text-xl font-bold text-red-600">{{ $studentFees->count() }} টি</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">মোট পরিমাণ</p>
                        <p class="text-xl font-bold text-blue-600">৳{{ number_format($studentFees->sum('due_amount'), 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">নির্বাচিত</p>
                        <p class="text-xl font-bold text-green-600">{{ count($selectedFees) }} টি</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">নির্বাচিত পরিমাণ</p>
                        <p class="text-xl font-bold text-green-600">৳{{ number_format($totalSelected, 0) }}</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <x-filament::button size="sm" color="gray" wire:click="selectAll">
                        সব নির্বাচন
                    </x-filament::button>
                    <x-filament::button size="sm" color="gray" wire:click="deselectAll">
                        সব বাতিল
                    </x-filament::button>
                    <x-filament::button size="sm" color="info" wire:click="exportPdf" icon="heroicon-o-document-arrow-down">
                        PDF
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Student List --}}
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" 
                                    class="rounded border-gray-300" 
                                    wire:click="{{ count($selectedFees) === $studentFees->count() ? 'deselectAll' : 'selectAll' }}"
                                    {{ count($selectedFees) === $studentFees->count() && $studentFees->count() > 0 ? 'checked' : '' }}>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">আইডি</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">ছাত্রের নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">শ্রেণি</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">ফি টাইপ</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">মোট</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">পরিশোধ</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">বকেয়া</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300">স্ট্যাটাস</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($studentFees as $fee)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ in_array($fee->id, $selectedFees) ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                <td class="px-4 py-3">
                                    <input type="checkbox" 
                                        class="rounded border-gray-300"
                                        value="{{ $fee->id }}"
                                        wire:model.live="selectedFees"
                                        wire:change="updateTotal">
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $fee->student->student_id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $fee->student->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $fee->student->class->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $fee->feeStructure->feeType->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">৳{{ number_format($fee->final_amount, 0) }}</td>
                                <td class="px-4 py-3 text-right text-green-600">৳{{ number_format($fee->paid_amount, 0) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-red-600">৳{{ number_format($fee->due_amount, 0) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($fee->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">বকেয়া</span>
                                    @elseif($fee->status === 'partial')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">আংশিক</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                    <x-heroicon-o-banknotes class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                    কোন বকেয়া ফি নেই
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Collection Button --}}
        @if(count($selectedFees) > 0)
            <div class="mt-4 flex justify-end">
                <x-filament::button 
                    size="lg" 
                    color="success" 
                    wire:click="collectFees"
                    wire:confirm="আপনি কি নিশ্চিত? {{ count($selectedFees) }} টি ফি আদায় করা হবে। মোট: ৳{{ number_format($totalSelected, 0) }}"
                    icon="heroicon-o-banknotes">
                    {{ count($selectedFees) }} টি ফি আদায় করুন (৳{{ number_format($totalSelected, 0) }})
                </x-filament::button>
            </div>
        @endif
    @endif
</x-filament-panels::page>
