<x-filament-panels::page>
    <form wire:submit="loadStudents">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                বকেয়া অনুসন্ধান
            </x-filament::button>
        </div>
    </form>

    @if($showStudents)
        {{-- SMS Info Alert --}}
        <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl">
            <div class="flex items-start gap-3">
                <x-heroicon-o-information-circle class="w-6 h-6 text-amber-600 mt-0.5" />
                <div>
                    <h4 class="font-semibold text-amber-800 dark:text-amber-200">SMS সার্ভিস তথ্য</h4>
                    <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                        SMS পাঠাতে হলে সেটিংস থেকে SMS Gateway কনফিগার করতে হবে। বর্তমানে SMS গুলো কিউতে জমা হবে।
                    </p>
                </div>
            </div>
        </div>

        {{-- Summary Bar --}}
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">মোট অভিভাবক</p>
                        <p class="text-xl font-bold text-blue-600">{{ $dueStudents->count() }} জন</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">মোট বকেয়া</p>
                        <p class="text-xl font-bold text-red-600">৳{{ number_format($dueStudents->sum('total_due'), 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">নির্বাচিত</p>
                        <p class="text-xl font-bold text-green-600">{{ count($selectedStudents) }} জন</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <x-filament::button size="sm" color="gray" wire:click="selectAll">
                        সব নির্বাচন
                    </x-filament::button>
                    <x-filament::button size="sm" color="gray" wire:click="deselectAll">
                        সব বাতিল
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
                                    wire:click="{{ count($selectedStudents) === $dueStudents->count() ? 'deselectAll' : 'selectAll' }}"
                                    {{ count($selectedStudents) === $dueStudents->count() && $dueStudents->count() > 0 ? 'checked' : '' }}>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">ছাত্রের নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">শ্রেণি</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">অভিভাবক</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">মোবাইল</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">বকেয়া</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300">ফি সংখ্যা</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($dueStudents as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ in_array($student['student_id'], $selectedStudents) ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                <td class="px-4 py-3">
                                    <input type="checkbox" 
                                        class="rounded border-gray-300"
                                        value="{{ $student['student_id'] }}"
                                        wire:model.live="selectedStudents">
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $student['student_name'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $student['class'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $student['guardian_name'] }}</td>
                                <td class="px-4 py-3">
                                    @if($student['phone'])
                                        <span class="inline-flex items-center gap-1 text-green-600">
                                            <x-heroicon-o-phone class="w-4 h-4" />
                                            {{ $student['phone'] }}
                                        </span>
                                    @else
                                        <span class="text-red-500 text-sm">নেই</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-red-600">৳{{ number_format($student['total_due'], 0) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $student['fee_count'] }} টি
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <x-heroicon-o-check-circle class="w-12 h-12 mx-auto mb-2 text-green-400" />
                                    কোন বকেয়া নেই!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Send SMS Button --}}
        @if(count($selectedStudents) > 0)
            <div class="mt-4 flex justify-end">
                <x-filament::button 
                    size="lg" 
                    color="success" 
                    wire:click="sendSms"
                    wire:confirm="আপনি কি নিশ্চিত? {{ count($selectedStudents) }} জন অভিভাবককে SMS পাঠানো হবে।"
                    icon="heroicon-o-paper-airplane">
                    {{ count($selectedStudents) }} জনকে SMS পাঠান
                </x-filament::button>
            </div>
        @endif

        {{-- SMS Sent Counter --}}
        @if($smsCount > 0)
            <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-green-600" />
                    <p class="text-green-700 dark:text-green-300">
                        <strong>{{ $smsCount }} টি</strong> SMS সফলভাবে কিউতে যোগ হয়েছে।
                    </p>
                </div>
            </div>
        @endif
    @endif
</x-filament-panels::page>
