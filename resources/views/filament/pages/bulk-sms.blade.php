<x-filament-panels::page>
    <form wire:submit="loadRecipients">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                প্রাপক খুঁজুন
            </x-filament::button>
        </div>
    </form>

    @if($showRecipients)
        {{-- Summary Bar --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">মোট প্রাপক</p>
                        <p class="text-xl font-bold text-blue-600">{{ $recipients->count() }} জন</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">নির্বাচিত</p>
                        <p class="text-xl font-bold text-green-600">{{ count($selectedRecipients) }} জন</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <x-filament::button size="sm" color="gray" wire:click="selectAll">
                        সব নির্বাচন
                    </x-filament::button>
                    <x-filament::button size="sm" color="gray" wire:click="deselectAll">
                        সব বাতিল
                    </x-filament::button>
                    @if(count($selectedRecipients) > 0)
                        <x-filament::button size="sm" color="success" wire:click="sendSms" icon="heroicon-o-paper-airplane">
                            {{ count($selectedRecipients) }} জনকে SMS পাঠান
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recipients Table --}}
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold w-10">
                                <input type="checkbox" class="rounded border-gray-300"
                                    @checked(count($selectedRecipients) === $recipients->count())
                                    wire:click="{{ count($selectedRecipients) === $recipients->count() ? 'deselectAll' : 'selectAll' }}">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">নাম</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">মোবাইল</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">ধরণ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">বিবরণ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recipients as $recipient)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-3">
                                                    <input type="checkbox" class="rounded border-gray-300" value="{{ $recipient['id'] }}"
                                                        wire:model.live="selectedRecipients">
                                                </td>
                                                <td class="px-4 py-3 font-medium">{{ $recipient['name'] }}</td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-mono bg-gray-100 text-gray-600">
                                                        {{ $recipient['phone'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $typeColor = match ($recipient['type']) {
                                                            'ছাত্র' => 'bg-blue-100 text-blue-800',
                                                            'শিক্ষক' => 'bg-green-100 text-green-800',
                                                            'কর্মচারী' => 'bg-orange-100 text-orange-800',
                                                            default => 'bg-gray-100 text-gray-800',
                                                        };
                                                    @endphp
                             <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $typeColor }}">
                                                        {{ $recipient['type'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $recipient['extra'] }}</td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <x-heroicon-o-users class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                    কোন প্রাপক পাওয়া যায়নি
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>