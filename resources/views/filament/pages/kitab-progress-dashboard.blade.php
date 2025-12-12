<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-chart-bar">
                প্রগ্রেস দেখুন
            </x-filament::button>

            @if($showDashboard)
                <x-filament::button color="success" wire:click="exportPdf" icon="heroicon-o-document-arrow-down">
                    PDF ডাউনলোড
                </x-filament::button>
            @endif
        </div>
    </form>

    @if($showDashboard)
        {{-- Overall Stats --}}
        <div class="mt-8 grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-users class="w-8 h-8 mx-auto text-blue-500" />
                <p class="text-2xl font-bold text-blue-600 mt-2">{{ $overallStats['total_students'] }}</p>
                <p class="text-sm text-gray-500">মোট ছাত্র</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-arrow-path class="w-8 h-8 mx-auto text-yellow-500" />
                <p class="text-2xl font-bold text-yellow-600 mt-2">{{ $overallStats['active_progress'] }}</p>
                <p class="text-sm text-gray-500">চলমান</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-check-circle class="w-8 h-8 mx-auto text-green-500" />
                <p class="text-2xl font-bold text-green-600 mt-2">{{ $overallStats['completed'] }}</p>
                <p class="text-sm text-gray-500">সম্পন্ন</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-clock class="w-8 h-8 mx-auto text-gray-500" />
                <p class="text-2xl font-bold text-gray-600 mt-2">{{ $overallStats['not_started'] }}</p>
                <p class="text-sm text-gray-500">শুরু হয়নি</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-document-text class="w-8 h-8 mx-auto text-purple-500" />
                <p class="text-2xl font-bold text-purple-600 mt-2">{{ $overallStats['total_pages'] }}</p>
                <p class="text-sm text-gray-500">মোট পৃষ্ঠা পড়া</p>
            </div>
        </div>

        {{-- Kitab Summary --}}
        @if(count($kitabSummary) > 0)
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <x-heroicon-o-book-open class="w-5 h-5 text-blue-500" />
                        কিতাব সারাংশ
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold">কিতাব</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold">অধ্যায়</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold">পাঠ</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold">সম্পন্ন (ছাত্র)</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold">গড় পৃষ্ঠা</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($kitabSummary as $kitab)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-medium">{{ $kitab['name'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $kitab['total_chapters'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $kitab['total_lessons'] }}</td>
                                    <td class="px-4 py-3 text-center text-green-600 font-semibold">{{ $kitab['completed_students'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $kitab['avg_pages'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Student Progress --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <x-heroicon-o-academic-cap class="w-5 h-5 text-green-500" />
                    ছাত্র প্রগ্রেস
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold">রোল</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">নাম</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">অধ্যায়</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">পাঠ</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">পৃষ্ঠা</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">শেষ পড়া</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">স্ট্যাটাস</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 font-mono text-sm">{{ $student['roll'] ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium">{{ $student['name'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $student['total_chapters'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $student['total_lessons'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-purple-600">{{ $student['total_pages'] }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $student['last_date'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusColor = match($student['status']) {
                                            'completed' => 'bg-green-100 text-green-800',
                                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                                            'revision' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                        $statusLabel = match($student['status']) {
                                            'completed' => 'সম্পন্ন',
                                            'in_progress' => 'চলমান',
                                            'revision' => 'রিভিশন',
                                            default => 'শুরু হয়নি',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <x-heroicon-o-book-open class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                    কোন ছাত্র পাওয়া যায়নি
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>
