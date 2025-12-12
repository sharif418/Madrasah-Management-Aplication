<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        <form wire:submit="analyze">
            {{ $this->form }}

            <div class="mt-4 flex gap-3">
                <x-filament::button type="submit" icon="heroicon-o-chart-bar">
                    বিশ্লেষণ করুন
                </x-filament::button>

                @if($analysisData)
                    <x-filament::button type="button" wire:click="downloadPdf" color="success"
                        icon="heroicon-o-arrow-down-tray">
                        PDF ডাউনলোড
                    </x-filament::button>
                @endif
            </div>
        </form>
    </div>

    @if($showAnalysis && $analysisData)
        {{-- Header --}}
        <div
            class="mb-6 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-indigo-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        {{ $analysisData['exam']->name ?? '' }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $analysisData['class']->name ?? '' }}
                        @if($analysisData['section']) - {{ $analysisData['section'] }} @endif
                    </p>
                </div>
                <div class="flex gap-4">
                    <div class="text-center px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <span
                            class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $analysisData['overall']['total_subjects'] }}</span>
                        <span class="text-xs block text-gray-500 dark:text-gray-400">বিষয়</span>
                    </div>
                    <div class="text-center px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <span
                            class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($analysisData['overall']['average_pass_rate'], 1) }}%</span>
                        <span class="text-xs block text-gray-500 dark:text-gray-400">গড় পাস হার</span>
                    </div>
                    <div class="text-center px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <span
                            class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $analysisData['overall']['overall_average'] }}</span>
                        <span class="text-xs block text-gray-500 dark:text-gray-400">গড় নম্বর</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Subject Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($analysisData['subjects'] as $subject)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    {{-- Subject Header --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                                📚 {{ $subject['subject_name'] }}
                            </h4>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                পূর্ণ নম্বর: {{ $subject['full_marks'] }} | পাস: {{ $subject['pass_marks'] }}
                            </span>
                        </div>
                    </div>

                    <div class="p-4 space-y-4">
                        {{-- Stats Row --}}
                        <div class="grid grid-cols-4 gap-3">
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-center">
                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $subject['total_students'] }}
                                </div>
                                <div class="text-xs text-blue-700 dark:text-blue-300">মোট</div>
                            </div>
                            <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-lg text-center">
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ $subject['passed'] }}</div>
                                <div class="text-xs text-green-700 dark:text-green-300">পাস</div>
                            </div>
                            <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-lg text-center">
                                <div class="text-lg font-bold text-red-600 dark:text-red-400">{{ $subject['failed'] }}</div>
                                <div class="text-xs text-red-700 dark:text-red-300">ফেল</div>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                <div class="text-lg font-bold text-gray-600 dark:text-gray-400">{{ $subject['absent'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">অনু.</div>
                            </div>
                        </div>

                        {{-- Pass Rate Bar --}}
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400">পাস হার</span>
                                <span
                                    class="font-semibold {{ $subject['pass_percentage'] >= 60 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $subject['pass_percentage'] }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                <div class="h-3 rounded-full {{ $subject['pass_percentage'] >= 60 ? 'bg-green-500' : 'bg-red-500' }}"
                                    style="width: {{ $subject['pass_percentage'] }}%"></div>
                            </div>
                        </div>

                        {{-- Marks Stats --}}
                        <div class="grid grid-cols-3 gap-3 text-center text-sm">
                            <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded">
                                <span
                                    class="block font-bold text-purple-600 dark:text-purple-400">{{ $subject['highest'] }}</span>
                                <span class="text-xs text-purple-700 dark:text-purple-300">সর্বোচ্চ</span>
                            </div>
                            <div class="p-2 bg-yellow-50 dark:bg-yellow-900/30 rounded">
                                <span
                                    class="block font-bold text-yellow-600 dark:text-yellow-400">{{ $subject['average'] }}</span>
                                <span class="text-xs text-yellow-700 dark:text-yellow-300">গড়</span>
                            </div>
                            <div class="p-2 bg-orange-50 dark:bg-orange-900/30 rounded">
                                <span
                                    class="block font-bold text-orange-600 dark:text-orange-400">{{ $subject['lowest'] }}</span>
                                <span class="text-xs text-orange-700 dark:text-orange-300">সর্বনিম্ন</span>
                            </div>
                        </div>

                        {{-- Grade Distribution --}}
                        <div>
                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">গ্রেড বিতরণ</h5>
                            <div class="flex gap-1">
                                @foreach($subject['grade_distribution'] as $grade => $count)
                                    <div class="flex-1 text-center p-1 rounded text-xs
                                        @if($grade === 'A+') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300
                                        @elseif($grade === 'A') bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300
                                        @elseif($grade === 'A-') bg-lime-100 text-lime-700 dark:bg-lime-900/50 dark:text-lime-300
                                        @elseif($grade === 'B') bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300
                                        @elseif($grade === 'C') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300
                                        @elseif($grade === 'D') bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300
                                        @else bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300
                                        @endif
                                    ">
                                        <div class="font-bold">{{ $count }}</div>
                                        <div class="text-[10px]">{{ $grade }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Top Performers --}}
                        @if(count($subject['top_performers']) > 0)
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">🏆 সেরা ৫ জন</h5>
                                <div class="space-y-1">
                                    @foreach($subject['top_performers'] as $index => $performer)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded text-sm">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold
                                                    {{ $index === 0 ? 'bg-yellow-400 text-yellow-900' : 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                                                    {{ $index + 1 }}
                                                </span>
                                                <span class="text-gray-700 dark:text-gray-200">{{ $performer['name'] }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">(রোল:
                                                    {{ $performer['roll'] ?? '-' }})</span>
                                            </div>
                                            <span class="font-semibold text-primary-600 dark:text-primary-400">
                                                {{ $performer['marks'] }} ({{ $performer['percentage'] }}%)
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <x-heroicon-o-chart-bar class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">বিষয় বিশ্লেষণ</h3>
            <p class="text-gray-500 dark:text-gray-400">পরীক্ষা ও শ্রেণি নির্বাচন করে "বিশ্লেষণ করুন" ক্লিক করুন।</p>
        </div>
    @endif
</x-filament-panels::page>