<x-filament-panels::page>
    @php
        $stats = $this->getMonthlyStats();
    @endphp

    <!-- Monthly Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">মোট দিন</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</h3>
        </div>
        <div
            class="bg-success-50 dark:bg-success-900/20 rounded-xl p-4 shadow-sm border border-success-200 dark:border-success-800 text-center">
            <p class="text-sm text-success-600 dark:text-success-400">উপস্থিত</p>
            <h3 class="text-2xl font-bold text-success-700 dark:text-success-300">{{ $stats['present'] }}</h3>
        </div>
        <div
            class="bg-danger-50 dark:bg-danger-900/20 rounded-xl p-4 shadow-sm border border-danger-200 dark:border-danger-800 text-center">
            <p class="text-sm text-danger-600 dark:text-danger-400">অনুপস্থিত</p>
            <h3 class="text-2xl font-bold text-danger-700 dark:text-danger-300">{{ $stats['absent'] }}</h3>
        </div>
        <div
            class="bg-warning-50 dark:bg-warning-900/20 rounded-xl p-4 shadow-sm border border-warning-200 dark:border-warning-800 text-center">
            <p class="text-sm text-warning-600 dark:text-warning-400">বিলম্বে</p>
            <h3 class="text-2xl font-bold text-warning-700 dark:text-warning-300">{{ $stats['late'] }}</h3>
        </div>
        <div
            class="bg-info-50 dark:bg-info-900/20 rounded-xl p-4 shadow-sm border border-info-200 dark:border-info-800 text-center">
            <p class="text-sm text-info-600 dark:text-info-400">ছুটি</p>
            <h3 class="text-2xl font-bold text-info-700 dark:text-info-300">{{ $stats['leave'] }}</h3>
        </div>
        <div
            class="bg-primary-50 dark:bg-primary-900/20 rounded-xl p-4 shadow-sm border border-primary-200 dark:border-primary-800 text-center">
            <p class="text-sm text-primary-600 dark:text-primary-400">শতকরা</p>
            <h3 class="text-2xl font-bold text-primary-700 dark:text-primary-300">{{ $stats['percentage'] }}%</h3>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">উপস্থিতি রেকর্ড ({{ now()->format('F Y') }})</h3>
        {{ $this->table }}
    </div>
</x-filament-panels::page>