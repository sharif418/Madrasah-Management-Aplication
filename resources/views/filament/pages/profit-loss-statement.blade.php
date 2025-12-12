<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-scale">
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
        {{-- Net Profit/Loss Card --}}
        <div class="mt-8 bg-gradient-to-r {{ $summary['is_profit'] ? 'from-green-500 to-emerald-600' : 'from-red-500 to-rose-600' }} rounded-2xl shadow-xl p-8 text-white">
            <div class="text-center">
                <p class="text-lg opacity-90">নেট {{ $summary['is_profit'] ? 'লাভ' : 'ক্ষতি' }}</p>
                <p class="text-5xl font-bold mt-2">৳{{ number_format(abs($summary['net_profit']), 0) }}</p>
                <p class="mt-2 opacity-80">
                    {{ $summary['profit_margin'] }}% {{ $summary['is_profit'] ? 'লাভের হার' : 'ক্ষতির হার' }}
                </p>
            </div>
        </div>

        {{-- Income & Expense Cards --}}
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Income Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="bg-green-500 text-white px-6 py-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <x-heroicon-o-arrow-down-circle class="w-5 h-5" />
                        আয় (Income)
                    </h3>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <tbody class="divide-y dark:divide-gray-700">
                            <tr>
                                <td class="py-3 font-medium">ফি আদায় (Fee Collection)</td>
                                <td class="py-3 text-right text-green-600 font-semibold">৳{{ number_format($incomeData['fee_collection'], 0) }}</td>
                            </tr>
                            @foreach($incomeData['other_incomes'] as $income)
                                <tr>
                                    <td class="py-3">{{ $income['head'] }}</td>
                                    <td class="py-3 text-right text-green-600">৳{{ number_format($income['amount'], 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-green-500">
                            <tr class="font-bold text-lg">
                                <td class="py-4">মোট আয়</td>
                                <td class="py-4 text-right text-green-600">৳{{ number_format($incomeData['total'], 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Expense Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="bg-red-500 text-white px-6 py-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <x-heroicon-o-arrow-up-circle class="w-5 h-5" />
                        ব্যয় (Expense)
                    </h3>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <tbody class="divide-y dark:divide-gray-700">
                            <tr>
                                <td class="py-3 font-medium">বেতন (Salary)</td>
                                <td class="py-3 text-right text-red-600 font-semibold">৳{{ number_format($expenseData['salary_expense'], 0) }}</td>
                            </tr>
                            @foreach($expenseData['other_expenses'] as $expense)
                                <tr>
                                    <td class="py-3">{{ $expense['head'] }}</td>
                                    <td class="py-3 text-right text-red-600">৳{{ number_format($expense['amount'], 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-red-500">
                            <tr class="font-bold text-lg">
                                <td class="py-4">মোট ব্যয়</td>
                                <td class="py-4 text-right text-red-600">৳{{ number_format($expenseData['total'], 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Summary Table --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <x-heroicon-o-document-text class="w-5 h-5 text-blue-500" />
                সংক্ষিপ্ত বিবরণী
            </h3>
            <table class="w-full">
                <tbody class="divide-y dark:divide-gray-700">
                    <tr>
                        <td class="py-3 font-medium">মোট আয়</td>
                        <td class="py-3 text-right text-green-600 font-semibold">৳{{ number_format($summary['total_income'], 0) }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 font-medium">মোট ব্যয়</td>
                        <td class="py-3 text-right text-red-600 font-semibold">৳{{ number_format($summary['total_expense'], 0) }}</td>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-gray-700 text-xl font-bold">
                        <td class="py-4 px-4 rounded-l-lg">নেট {{ $summary['is_profit'] ? 'লাভ' : 'ক্ষতি' }}</td>
                        <td class="py-4 px-4 text-right rounded-r-lg {{ $summary['is_profit'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ $summary['is_profit'] ? '' : '-' }}৳{{ number_format(abs($summary['net_profit']), 0) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
