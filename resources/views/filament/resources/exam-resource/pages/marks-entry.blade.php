<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="saveMarks">
            {{ $this->form }}

            <div class="mt-4 flex gap-2">
                <x-filament::button type="button" wire:click="loadStudents" color="primary">
                    ছাত্র লোড করুন
                </x-filament::button>
            </div>

            @if($showStudents && $students->count() > 0)
                <div class="mt-6">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
                        <!-- Header -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        নম্বর এন্ট্রি - {{ $currentSchedule?->subject?->name ?? 'বিষয়' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        মোট ছাত্র: {{ $students->count() }} জন | পূর্ণ নম্বর:
                                        {{ $currentSchedule?->full_marks ?? 100 }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <x-filament::button type="button" wire:click="markAllAbsent" color="warning" size="sm">
                                        সকলকে অনুপস্থিত
                                    </x-filament::button>
                                    <x-filament::button type="button" wire:click="clearAllAbsent" color="gray" size="sm">
                                        অনুপস্থিত মুছুন
                                    </x-filament::button>
                                </div>
                            </div>
                        </div>

                        <!-- Marks Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                            রোল
                                        </th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            নাম
                                        </th>
                                        <th
                                            class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">
                                            লিখিত
                                        </th>
                                        <th
                                            class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">
                                            MCQ
                                        </th>
                                        <th
                                            class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">
                                            ব্যবহারিক
                                        </th>
                                        <th
                                            class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">
                                            মৌখিক
                                        </th>
                                        <th
                                            class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                            অনুপস্থিত
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($students as $index => $student)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ ($marksData[$student->id]['is_absent'] ?? false) ? 'bg-red-50 dark:bg-red-900/20' : '' }}"
                                            wire:key="marks-{{ $student->id }}">
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->roll_no ?? ($index + 1) }}
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $student->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $student->admission_no }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-center">
                                                <input type="number"
                                                    wire:model.live="marksData.{{ $student->id }}.written_marks" step="0.5"
                                                    min="0"
                                                    class="w-16 text-center text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50"
                                                    placeholder="0" {{ ($marksData[$student->id]['is_absent'] ?? false) ? 'disabled' : '' }}>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-center">
                                                <input type="number" wire:model.live="marksData.{{ $student->id }}.mcq_marks"
                                                    step="0.5" min="0"
                                                    class="w-16 text-center text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50"
                                                    placeholder="0" {{ ($marksData[$student->id]['is_absent'] ?? false) ? 'disabled' : '' }}>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-center">
                                                <input type="number"
                                                    wire:model.live="marksData.{{ $student->id }}.practical_marks" step="0.5"
                                                    min="0"
                                                    class="w-16 text-center text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50"
                                                    placeholder="0" {{ ($marksData[$student->id]['is_absent'] ?? false) ? 'disabled' : '' }}>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-center">
                                                <input type="number" wire:model.live="marksData.{{ $student->id }}.viva_marks"
                                                    step="0.5" min="0"
                                                    class="w-16 text-center text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50"
                                                    placeholder="0" {{ ($marksData[$student->id]['is_absent'] ?? false) ? 'disabled' : '' }}>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-center">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox"
                                                        wire:model.live="marksData.{{ $student->id }}.is_absent"
                                                        class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700">
                                                    <span class="ml-2 text-xs text-gray-600 dark:text-gray-400">অনুপস্থিত</span>
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="text-red-500">*</span> অনুপস্থিত চিহ্নিত করলে সব নম্বর ০ হবে
                                </p>
                                <x-filament::button type="submit" color="success" size="lg">
                                    <x-heroicon-o-check class="w-5 h-5 mr-2" />
                                    নম্বর সংরক্ষণ করুন
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($showStudents && $students->count() === 0)
                <div class="mt-6 p-8 text-center bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <x-heroicon-o-user-group class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">কোন ছাত্র পাওয়া যায়নি</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">এই শ্রেণিতে সক্রিয় ছাত্র নেই</p>
                </div>
            @endif
        </form>
    </div>
</x-filament-panels::page>