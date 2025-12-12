<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-chart-bar-square">
                রিপোর্ট দেখুন
            </x-filament::button>

            @if($showReport)
                <x-filament::button color="success" wire:click="exportPdf" icon="heroicon-o-document-arrow-down">
                    PDF ডাউনলোড
                </x-filament::button>
            @endif
        </div>
    </form>

    @if($showReport)
        {{-- Summary Cards --}}
        <div class="mt-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-book-open class="w-8 h-8 mx-auto text-blue-500" />
                <p class="text-2xl font-bold text-blue-600 mt-2">{{ $summary['total_books'] }}</p>
                <p class="text-sm text-gray-500">মোট বই</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-square-3-stack-3d class="w-8 h-8 mx-auto text-purple-500" />
                <p class="text-2xl font-bold text-purple-600 mt-2">{{ $summary['total_copies'] }}</p>
                <p class="text-sm text-gray-500">মোট কপি</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-check-circle class="w-8 h-8 mx-auto text-green-500" />
                <p class="text-2xl font-bold text-green-600 mt-2">{{ $summary['available_copies'] }}</p>
                <p class="text-sm text-gray-500">সরবরাহযোগ্য</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-arrow-right-circle class="w-8 h-8 mx-auto text-orange-500" />
                <p class="text-2xl font-bold text-orange-600 mt-2">{{ $summary['issued_copies'] }}</p>
                <p class="text-sm text-gray-500">জারিকৃত</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-rectangle-stack class="w-8 h-8 mx-auto text-indigo-500" />
                <p class="text-2xl font-bold text-indigo-600 mt-2">{{ $summary['categories'] }}</p>
                <p class="text-sm text-gray-500">ক্যাটাগরি</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center">
                <x-heroicon-o-banknotes class="w-8 h-8 mx-auto text-emerald-500" />
                <p class="text-2xl font-bold text-emerald-600 mt-2">৳{{ number_format($summary['total_value'], 0) }}</p>
                <p class="text-sm text-gray-500">মোট মূল্য</p>
            </div>
        </div>

        {{-- Category-wise Table --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <x-heroicon-o-rectangle-stack class="w-5 h-5 text-blue-500" />
                    ক্যাটাগরি অনুযায়ী স্টক
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold">ক্যাটাগরি</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">বই</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">মোট কপি</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">সরবরাহযোগ্য</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">জারিকৃত</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold">মূল্য</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($categoryWise as $cat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 font-medium">{{ $cat['name'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $cat['book_count'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $cat['total_copies'] }}</td>
                                <td class="px-4 py-3 text-center text-green-600">{{ $cat['available'] }}</td>
                                <td class="px-4 py-3 text-center text-orange-600">{{ $cat['issued'] }}</td>
                                <td class="px-4 py-3 text-right">৳{{ number_format($cat['value'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <x-heroicon-o-clock class="w-5 h-5 text-purple-500" />
                    সাম্প্রতিক কার্যক্রম
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold">তারিখ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">বই</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">সদস্য</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold">কার্যক্রম</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentActivity as $activity)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-sm">{{ $activity['date'] }}</td>
                                <td class="px-4 py-3 font-medium">{{ Str::limit($activity['book'], 30) }}</td>
                                <td class="px-4 py-3">{{ $activity['member'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $activity['status'] === 'issued' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $activity['action'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">কোন কার্যক্রম নেই</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>