<x-filament-panels::page>
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">মোট বকেয়া</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                        ৳{{ number_format($this->getTotalDue(), 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                    <x-heroicon-o-user-group class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">বকেয়াদার ছাত্র</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $this->getTotalStudents() }}
                        জন</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex items-center justify-center">
            <x-filament::button wire:click="exportPdf" color="danger">
                <x-heroicon-o-document-arrow-down class="w-5 h-5 mr-2" />
                PDF ডাউনলোড
            </x-filament::button>
        </div>
    </div>

    {{-- Table --}}
    {{ $this->table }}
</x-filament-panels::page>