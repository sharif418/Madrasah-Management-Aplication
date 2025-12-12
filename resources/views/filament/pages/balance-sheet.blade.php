<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-rectangle-stack">
                ব্যালেন্স শীট দেখুন
            </x-filament::button>

            @if($showReport)
                <x-filament::button color="success" wire:click="exportPdf" icon="heroicon-o-document-arrow-down">
                    PDF ডাউনলোড
                </x-filament::button>
            @endif
        </div>
    </form>

    @if($showReport)
        {{-- Net Worth Card --}}
        <div class="mt-8 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-xl p-8 text-white text-center">
            <p class="text-lg opacity-90">নিট সম্পদ (Net Worth)</p>
            <p class="text-5xl font-bold mt-2">৳{{ number_format($summary['net_worth'], 0) }}</p>
            <p class="mt-2 opacity-80">{{ $summary['as_of_date'] }} পর্যন্ত</p>
        </div>

        {{-- Assets & Liabilities --}}
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Assets --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="bg-green-500 text-white px-6 py-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <x-heroicon-o-arrow-down-circle class="w-5 h-5" />
                        সম্পদ (Assets)
                    </h3>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($assets as $asset)
                                <tr>
                                    <td class="py-3">{{ $asset['name'] }}</td>
                                    <td class="py-3 text-right text-green-600 font-semibold">
                                        ৳{{ number_format($asset['amount'], 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-green-500">
                            <tr class="font-bold text-lg">
                                <td class="py-4">মোট সম্পদ</td>
                                <td class="py-4 text-right text-green-600">
                                    ৳{{ number_format($summary['total_assets'], 0) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Liabilities & Equity --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="bg-red-500 text-white px-6 py-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <x-heroicon-o-arrow-up-circle class="w-5 h-5" />
                        দায় ও মূলধন
                    </h3>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <tbody class="divide-y dark:divide-gray-700">
                            {{-- Liabilities --}}
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <td class="py-2 font-semibold" colspan="2">দায় (Liabilities)</td>
                            </tr>
                            @foreach($liabilities as $liability)
                                <tr>
                                    <td class="py-3 pl-4">{{ $liability['name'] }}</td>
                                    <td class="py-3 text-right text-red-600">
                                        ৳{{ number_format($liability['amount'], 0) }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="font-semibold">
                                <td class="py-3 pl-4">মোট দায়</td>
                                <td class="py-3 text-right text-red-600">
                                    ৳{{ number_format($summary['total_liabilities'], 0) }}
                                </td>
                            </tr>

                            {{-- Equity --}}
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <td class="py-2 font-semibold" colspan="2">মূলধন (Equity)</td>
                            </tr>
                            <tr class="font-bold text-lg">
                                <td class="py-3 pl-4">নিট সম্পদ</td>
                                <td class="py-3 text-right text-blue-600">
                                    ৳{{ number_format($summary['net_worth'], 0) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="border-t-2 border-red-500">
                            <tr class="font-bold text-lg">
                                <td class="py-4">মোট দায় ও মূলধন</td>
                                <td class="py-4 text-right">
                                    ৳{{ number_format($summary['total_assets'], 0) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>