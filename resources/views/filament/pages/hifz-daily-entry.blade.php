<x-filament-panels::page>
    <form wire:submit="loadStudents">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                ছাত্র লোড করুন
            </x-filament::button>
        </div>
    </form>

    @if($showForm && $students->count() > 0)
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <x-heroicon-o-book-open class="w-5 h-5 text-green-500" />
                    দৈনিক হিফজ প্রগ্রেস
                </h3>
                <x-filament::button color="success" wire:click="saveEntries" icon="heroicon-o-check">
                    সংরক্ষণ করুন
                </x-filament::button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold" rowspan="2">রোল</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold" rowspan="2">নাম</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold bg-green-50 dark:bg-green-900"
                                colspan="3">সাবাক (নতুন পড়া)</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold bg-blue-50 dark:bg-blue-900" colspan="2">
                                সাবকী (পুরাতন)</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold bg-purple-50 dark:bg-purple-900"
                                colspan="2">মানযিল (দোহার)</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold bg-amber-50 dark:bg-amber-900"
                                colspan="2">তাজবীদ/কিরাত</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold" rowspan="2">মন্তব্য</th>
                        </tr>
                        <tr>
                            <th class="px-2 py-1 text-xs bg-green-50 dark:bg-green-900">পারা</th>
                            <th class="px-2 py-1 text-xs bg-green-50 dark:bg-green-900">লাইন</th>
                            <th class="px-2 py-1 text-xs bg-green-50 dark:bg-green-900">মান</th>
                            <th class="px-2 py-1 text-xs bg-blue-50 dark:bg-blue-900">পারা</th>
                            <th class="px-2 py-1 text-xs bg-blue-50 dark:bg-blue-900">মান</th>
                            <th class="px-2 py-1 text-xs bg-purple-50 dark:bg-purple-900">থেকে-পর্যন্ত</th>
                            <th class="px-2 py-1 text-xs bg-purple-50 dark:bg-purple-900">মান</th>
                            <th class="px-2 py-1 text-xs bg-amber-50 dark:bg-amber-900">পাঠ/সূরা</th>
                            <th class="px-2 py-1 text-xs bg-amber-50 dark:bg-amber-900">মান</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-3 py-2 font-mono">{{ $student->roll ?? '-' }}</td>
                                <td class="px-3 py-2 font-medium">{{ Str::limit($student->name, 20) }}</td>

                                {{-- Sabaq --}}
                                <td class="px-1 py-1 bg-green-50 dark:bg-green-900/30">
                                    <select wire:model="entries.{{ $student->id }}.sabaq_para"
                                        class="w-16 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                                        <option value="">-</option>
                                        @for($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td class="px-1 py-1 bg-green-50 dark:bg-green-900/30">
                                    <input type="number" wire:model="entries.{{ $student->id }}.sabaq_lines"
                                        class="w-12 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600"
                                        placeholder="0">
                                </td>
                                <td class="px-1 py-1 bg-green-50 dark:bg-green-900/30">
                                    <select wire:model="entries.{{ $student->id }}.sabaq_quality"
                                        class="w-16 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                                        <option value="">-</option>
                                        <option value="excellent">অতি উত্তম</option>
                                        <option value="good">উত্তম</option>
                                        <option value="average">মধ্যম</option>
                                        <option value="poor">দুর্বল</option>
                                    </select>
                                </td>

                                {{-- Sabqi --}}
                                <td class="px-1 py-1 bg-blue-50 dark:bg-blue-900/30">
                                    <select wire:model="entries.{{ $student->id }}.sabqi_para"
                                        class="w-16 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                                        <option value="">-</option>
                                        @for($i = 1; $i <= 30; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td class="px-1 py-1 bg-blue-50 dark:bg-blue-900/30">
                                    <select wire:model="entries.{{ $student->id }}.sabqi_quality"
                                        class="w-16 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                                        <option value="">-</option>
                                        <option value="excellent">অতি উত্তম</option>
                                        <option value="good">উত্তম</option>
                                        <option value="average">মধ্যম</option>
                                        <option value="poor">দুর্বল</option>
                                    </select>
                                </td>

                                {{-- Manzil --}}
                                <td class="px-1 py-1 bg-purple-50 dark:bg-purple-900/30">
                                    <div class="flex gap-1">
                                        <select wire:model="entries.{{ $student->id }}.manzil_para_from"
                                            class="w-12 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                                            <option value="">-</option>
                                            @for($i = 1; $i <= 30; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <select wire:model="entries.{{ $student->id }}.manzil_para_to"
                                            class="w-12 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                                            <option value="">-</option>
                                            @for($i = 1; $i <= 30; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </td>
                                <td class="px-1 py-1 bg-purple-50 dark:bg-purple-900/30">
                                    <select wire:model="entries.{{ $student->id }}.manzil_quality"
                                        class="w-16 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                                        <option value="">-</option>
                                        <option value="excellent">অতি উত্তম</option>
                                        <option value="good">উত্তম</option>
                                        <option value="average">মধ্যম</option>
                                        <option value="poor">দুর্বল</option>
                                    </select>
                                </td>

                                {{-- Tajweed/Qirat --}}
                                <td class="px-1 py-1 bg-amber-50 dark:bg-amber-900/30">
                                    <input type="text" wire:model="entries.{{ $student->id }}.tajweed_lesson"
                                        class="w-16 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600"
                                        placeholder="পাঠ/সূরা">
                                </td>
                                <td class="px-1 py-1 bg-amber-50 dark:bg-amber-900/30">
                                    <select wire:model="entries.{{ $student->id }}.tajweed_quality"
                                        class="w-16 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                                        <option value="">-</option>
                                        <option value="excellent">অতি উত্তম</option>
                                        <option value="good">উত্তম</option>
                                        <option value="average">মধ্যম</option>
                                        <option value="poor">দুর্বল</option>
                                    </select>
                                </td>

                                {{-- Remarks --}}
                                <td class="px-1 py-1">
                                    <input type="text" wire:model="entries.{{ $student->id }}.teacher_remarks"
                                        class="w-24 text-xs rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600"
                                        placeholder="মন্তব্য...">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>