<x-filament-panels::page>
    <form wire:submit="preview">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-eye">
                প্রিভিউ দেখুন
            </x-filament::button>

            @if($showCertificate && $progressSummary['is_complete'])
                <x-filament::button color="success" wire:click="printCertificate" icon="heroicon-o-printer">
                    সার্টিফিকেট প্রিন্ট
                </x-filament::button>
            @endif
        </div>
    </form>

    @if($showCertificate && $student)
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            {{-- Status Banner --}}
            @if($progressSummary['is_complete'])
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <x-heroicon-o-check-badge class="w-6 h-6 mr-2" />
                        <p class="font-semibold">✅ হিফজ সম্পন্ন! সার্টিফিকেট প্রিন্ট করা যাবে।</p>
                    </div>
                </div>
            @else
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6 mr-2" />
                        <p class="font-semibold">⚠️ হিফজ এখনও সম্পন্ন হয়নি ({{ $progressSummary['completed_paras_count'] }}/30
                            পারা)</p>
                    </div>
                </div>
            @endif

            {{-- Student Info --}}
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">ছাত্রের তথ্য</h4>
                    <table class="w-full text-sm">
                        <tr>
                            <td class="py-1 text-gray-500">নাম:</td>
                            <td class="py-1 font-semibold">{{ $student->name }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">পিতার নাম:</td>
                            <td class="py-1">{{ $student->father_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">ক্লাস:</td>
                            <td class="py-1">{{ $student->class?->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">হিফজ সারাংশ</h4>
                    <table class="w-full text-sm">
                        <tr>
                            <td class="py-1 text-gray-500">সম্পন্ন পারা:</td>
                            <td class="py-1 font-semibold text-green-600">{{ $progressSummary['completed_paras_count'] }}/30
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">শুরুর তারিখ:</td>
                            <td class="py-1">{{ $progressSummary['start_date'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">শেষ তারিখ:</td>
                            <td class="py-1">{{ $progressSummary['completion_date'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-1 text-gray-500">মোট দিন:</td>
                            <td class="py-1">{{ $progressSummary['total_days'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Para Progress --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">পারা অগ্রগতি</h4>
                <div class="grid grid-cols-10 gap-2">
                    @for($i = 1; $i <= 30; $i++)
                                    @php
                                        $isComplete = in_array($i, $progressSummary['completed_paras']);
                                    @endphp
                           <div
                                        class="text-center py-2 rounded {{ $isComplete ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                                        {{ $i }}
                                    </div>
                    @endfor
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>