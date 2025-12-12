<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            {{ $this->form }}
        </div>

        @php $data = $this->getResults(); @endphp

        @if($data['results']->isNotEmpty())
            <!-- Results Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($data['results'] as $result)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-teal-500 to-cyan-600 text-white">
                            <h4 class="font-semibold">{{ $result->exam->name ?? 'N/A' }}</h4>
                            <p class="text-sm opacity-80">{{ $result->exam->academicYear->name ?? '' }}</p>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($result->total_marks, 1) }}</div>
                                    <div class="text-xs text-gray-500">মোট নম্বর</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($result->percentage, 1) }}%</div>
                                    <div class="text-xs text-gray-500">শতাংশ</div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div>
                                    <span
                                        class="px-3 py-1 rounded-full text-sm font-bold
                                                {{ $result->result_status === 'pass' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $result->result_status === 'pass' ? 'পাস' : 'ফেল' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-bold text-teal-600">{{ $result->grade ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">GPA: {{ $result->gpa ?? 'N/A' }}</div>
                                </div>
                            </div>
                            @if($result->position)
                                <div class="mt-4 text-center bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded-lg">
                                    <span class="text-yellow-700 dark:text-yellow-400 font-semibold">
                                        🏆 মেধা ক্রম: {{ $result->position }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Subject-wise Marks (if exam selected) -->
            @if($data['marks']->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-document-text class="w-5 h-5 text-teal-500" />
                            বিষয়ভিত্তিক নম্বর
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        বিষয়</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        লিখিত</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        MCQ</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ব্যবহারিক</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        মৌখিক</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        মোট</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        গ্রেড</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($data['marks'] as $mark)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                                    {{ $mark->subject->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600 dark:text-gray-400">
                                                    {{ $mark->is_absent ? 'অনুপস্থিত' : ($mark->written_marks ?? '-') }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600 dark:text-gray-400">
                                                    {{ $mark->mcq_marks ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600 dark:text-gray-400">
                                                    {{ $mark->practical_marks ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600 dark:text-gray-400">
                                                    {{ $mark->viva_marks ?? '-' }}
                                                </td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-center font-semibold text-gray-900 dark:text-white">
                                                    {{ $mark->total_marks ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <span
                                                        class="px-2 py-1 text-xs font-bold rounded
                                                                    {{ $mark->grade?->name === 'A+' ? 'bg-green-100 text-green-700' :
                                    ($mark->grade?->name === 'F' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                                        {{ $mark->grade?->name ?? '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-12 text-center">
                <x-heroicon-o-academic-cap class="w-16 h-16 mx-auto mb-4 text-gray-400" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">কোন ফলাফল পাওয়া যায়নি</h3>
                <p class="text-gray-500 mt-2">সন্তান নির্বাচন করুন বা ফলাফল প্রকাশিত হয়নি।</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>