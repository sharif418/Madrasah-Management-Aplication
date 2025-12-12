<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Welcome Message -->
        <div class="bg-gradient-to-r from-teal-500 to-cyan-600 rounded-xl p-6 text-white">
            <h2 class="text-2xl font-bold mb-2">আসসালামু আলাইকুম, {{ auth()->user()->name }}!</h2>
            <p class="opacity-90">অভিভাবক পোর্টালে স্বাগতম। এখান থেকে আপনি আপনার সন্তানদের সকল তথ্য দেখতে পারবেন।</p>
        </div>

        <!-- Stats Cards -->
        @php $stats = $this->getStats(); @endphp
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-teal-100 dark:bg-teal-900 rounded-lg">
                        <x-heroicon-o-users class="w-6 h-6 text-teal-600 dark:text-teal-400" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_children'] }}
                        </div>
                        <div class="text-sm text-gray-500">সন্তান সংখ্যা</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <x-heroicon-o-check-badge class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $stats['attendance_percent'] }}%</div>
                        <div class="text-sm text-gray-500">এই মাসের হাজিরা</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div
                        class="p-3 {{ $stats['total_due'] > 0 ? 'bg-red-100 dark:bg-red-900' : 'bg-green-100 dark:bg-green-900' }} rounded-lg">
                        <x-heroicon-o-currency-bangladeshi
                            class="w-6 h-6 {{ $stats['total_due'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}" />
                    </div>
                    <div>
                        <div
                            class="text-2xl font-bold {{ $stats['total_due'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ৳{{ number_format($stats['total_due'], 0) }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $stats['total_due'] > 0 ? 'বকেয়া আছে' : 'কোন বকেয়া নেই' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        @if($stats['latest_result'])
                            <div class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $stats['latest_result']->grade ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">সর্বশেষ গ্রেড</div>
                        @else
                            <div class="text-sm text-gray-500">ফলাফল নেই</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Children List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-user-group class="w-5 h-5" />
                    আমার সন্তানগণ
                </h3>
            </div>
            <div class="p-4">
                @php $children = $this->getChildren(); @endphp
                @if($children->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <x-heroicon-o-user-plus class="w-12 h-12 mx-auto mb-3 opacity-50" />
                        <p>কোন সন্তান যুক্ত নেই। অনুগ্রহ করে প্রশাসনের সাথে যোগাযোগ করুন।</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($children as $child)
                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-16 h-16 rounded-full bg-teal-100 dark:bg-teal-900 flex items-center justify-center text-teal-600 dark:text-teal-400 text-xl font-bold">
                                        {{ mb_substr($child->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $child->name }}</h4>
                                        <p class="text-sm text-gray-500">রোল: {{ $child->roll_no ?? 'N/A' }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span
                                                class="px-2 py-1 bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300 text-xs rounded-full">
                                                {{ $child->class->name ?? 'N/A' }}
                                            </span>
                                            @if($child->section)
                                                <span
                                                    class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-xs rounded-full">
                                                    {{ $child->section->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('filament.parent.pages.my-children') }}"
                class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700 hover:border-teal-500 transition-colors group">
                <div class="text-center">
                    <x-heroicon-o-user-group class="w-8 h-8 mx-auto mb-2 text-gray-400 group-hover:text-teal-500" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">সন্তানদের তথ্য</span>
                </div>
            </a>
            <a href="{{ route('filament.parent.pages.attendance') }}"
                class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700 hover:border-teal-500 transition-colors group">
                <div class="text-center">
                    <x-heroicon-o-calendar-days class="w-8 h-8 mx-auto mb-2 text-gray-400 group-hover:text-teal-500" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">হাজিরা</span>
                </div>
            </a>
            <a href="{{ route('filament.parent.pages.results') }}"
                class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700 hover:border-teal-500 transition-colors group">
                <div class="text-center">
                    <x-heroicon-o-document-chart-bar
                        class="w-8 h-8 mx-auto mb-2 text-gray-400 group-hover:text-teal-500" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ফলাফল</span>
                </div>
            </a>
            <a href="{{ route('filament.parent.pages.fees') }}"
                class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700 hover:border-teal-500 transition-colors group">
                <div class="text-center">
                    <x-heroicon-o-banknotes class="w-8 h-8 mx-auto mb-2 text-gray-400 group-hover:text-teal-500" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ফি/বকেয়া</span>
                </div>
            </a>
        </div>
    </div>
</x-filament-panels::page>