<x-filament-panels::page>
    <div class="space-y-6">
        @if(count($studentMarks) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-gray-800 dark:to-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $record->name }} - ফলাফল
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                শ্রেণি: {{ $record->class?->name }} | মোট পরীক্ষার্থী: {{ count($studentMarks) }} জন
                            </p>
                        </div>
                        <div class="flex gap-4 text-sm">
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full">
                                পাস: {{ collect($studentMarks)->where('is_passed', true)->count() }}
                            </span>
                            <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-full">
                                ফেল: {{ collect($studentMarks)->where('is_passed', false)->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Results Table -->
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                    অবস্থান
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                    ছাত্রের নাম
                                </th>
                                @foreach($subjects as $subject)
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ $subject->name }}
                                    </th>
                                @endforeach
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                    মোট
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                    %
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                    গ্রেড
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                    GPA
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                    ফলাফল
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($studentMarks as $studentId => $data)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ !$data['is_passed'] ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                            {{ $data['position'] <= 3 ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-bold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                            {{ $data['position'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $data['student']->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            রোল: {{ $data['student']->roll_no }} | {{ $data['student']->admission_no }}
                                        </div>
                                    </td>
                                    @foreach($subjects as $subject)
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            @if(isset($data['subjects'][$subject->id]))
                                                @php $subjectMark = $data['subjects'][$subject->id]; @endphp
                                                <span class="{{ $subjectMark['passed'] ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400 font-bold' }}">
                                                    {{ $subjectMark['marks'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3 whitespace-nowrap text-center font-bold text-gray-900 dark:text-white">
                                        {{ $data['total_obtained'] }}/{{ $data['total_full'] }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                        {{ $data['percentage'] }}%
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-sm font-medium">
                                            {{ $data['grade'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center font-medium text-gray-900 dark:text-white">
                                        {{ $data['gpa'] }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        @if($data['is_passed'])
                                            <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-medium">
                                                পাস
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-full text-xs font-medium">
                                                ফেল
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="p-8 text-center bg-gray-50 dark:bg-gray-800 rounded-xl">
                <x-heroicon-o-clipboard-document-list class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500"/>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">ফলাফল নেই</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    এই পরীক্ষার জন্য নম্বর এন্ট্রি করা হয়নি। প্রথমে নম্বর এন্ট্রি করুন।
                </p>
                <div class="mt-4">
                    <x-filament::button
                        :href="$this->getResource()::getUrl('marks-entry', ['record' => $record])"
                        tag="a"
                        color="primary"
                    >
                        নম্বর এন্ট্রি করুন
                    </x-filament::button>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
