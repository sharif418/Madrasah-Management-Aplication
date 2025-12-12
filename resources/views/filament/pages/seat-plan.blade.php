<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        <form wire:submit="generateSeatPlan">
            {{ $this->form }}

            <div class="mt-4 flex gap-3">
                <x-filament::button type="submit" icon="heroicon-o-table-cells">
                    আসন বিন্যাস তৈরি
                </x-filament::button>

                @if($seatPlanData)
                    <x-filament::button type="button" wire:click="downloadPdf" color="success"
                        icon="heroicon-o-arrow-down-tray">
                        PDF ডাউনলোড
                    </x-filament::button>
                @endif
            </div>
        </form>
    </div>

    @if($showPlan && $seatPlanData)
        {{-- Summary Header --}}
        <div
            class="mb-6 p-4 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-primary-200 dark:border-gray-700">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        {{ $seatPlanData['exam']->name ?? 'পরীক্ষা' }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        শ্রেণি: {{ implode(', ', $seatPlanData['classes']) }} |
                        {{ $seatPlanData['exam']->academicYear?->name ?? '' }}
                    </p>
                </div>
                <div class="flex gap-4">
                    <div class="text-center px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <span
                            class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $seatPlanData['total_students'] }}</span>
                        <span class="text-xs block text-gray-500 dark:text-gray-400">মোট ছাত্র</span>
                    </div>
                    <div class="text-center px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <span
                            class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $seatPlanData['allocated'] }}</span>
                        <span class="text-xs block text-gray-500 dark:text-gray-400">বসানো</span>
                    </div>
                    <div class="text-center px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <span
                            class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ count($seatPlanData['rooms']) }}</span>
                        <span class="text-xs block text-gray-500 dark:text-gray-400">রুম</span>
                    </div>
                    @if($seatPlanData['unallocated'] > 0)
                        <div class="text-center px-4 py-2 bg-red-100 dark:bg-red-900/30 rounded-lg shadow-sm">
                            <span
                                class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $seatPlanData['unallocated'] }}</span>
                            <span class="text-xs block text-red-500 dark:text-red-400">বাকি</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Room Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($seatPlanData['rooms'] as $roomIndex => $room)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    {{-- Room Header --}}
                    <div
                        class="p-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                                🚪 রুম: {{ $room['room_name'] }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                ছাত্র সংখ্যা: {{ $room['total_students'] }}
                            </p>
                        </div>
                        <span
                            class="px-3 py-1 bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-full text-sm font-medium">
                            রুম {{ $roomIndex + 1 }}
                        </span>
                    </div>

                    {{-- Seat Grid --}}
                    <div class="p-4 overflow-x-auto">
                        <table class="w-full text-xs">
                            <tbody>
                                @foreach($room['rows'] as $rowIndex => $row)
                                    <tr>
                                        <td class="p-1 text-center font-bold text-gray-400 dark:text-gray-500 w-8">
                                            {{ $rowIndex + 1 }}
                                        </td>
                                        @foreach($row as $seat)
                                            <td class="p-1">
                                                <div
                                                    class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-600 text-center min-w-[80px]">
                                                    <div class="text-[10px] text-gray-500 dark:text-gray-400 mb-0.5">
                                                        {{ $seat['class'] }} | রোল {{ $seat['roll'] ?? '-' }}
                                                    </div>
                                                    <div class="font-medium text-gray-700 dark:text-gray-200 truncate"
                                                        title="{{ $seat['name'] }}">
                                                        {{ $seat['name'] }}
                                                    </div>
                                                    <div class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">
                                                        আসন: {{ $seat['seat'] }}
                                                    </div>
                                                </div>
                                            </td>
                                        @endforeach
                                        {{-- Fill empty cells --}}
                                        @for($i = count($row); $i < $seatPlanData['students_per_row']; $i++)
                                            <td class="p-1">
                                                <div
                                                    class="p-2 bg-gray-100 dark:bg-gray-800 rounded border border-dashed border-gray-300 dark:border-gray-600 text-center min-w-[80px] opacity-50">
                                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                                </div>
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <x-heroicon-o-table-cells class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">আসন বিন্যাস তৈরি করুন</h3>
            <p class="text-gray-500 dark:text-gray-400">পরীক্ষা, শ্রেণি এবং রুম নির্বাচন করে "আসন বিন্যাস তৈরি" ক্লিক করুন।
            </p>
        </div>
    @endif
</x-filament-panels::page>