<x-filament-panels::page>
    <form wire:submit="loadMembers">
        {{ $this->form }}

        <div class="mt-4 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                সদস্য খুঁজুন
            </x-filament::button>
        </div>
    </form>

    @if($showMembers)
        {{-- Summary Bar --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">মোট সদস্য</p>
                        <p class="text-xl font-bold text-blue-600">{{ $members->count() }} জন</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">নির্বাচিত</p>
                        <p class="text-xl font-bold text-green-600">{{ count($selectedMembers) }} জন</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <x-filament::button size="sm" color="gray" wire:click="selectAll">
                        সব নির্বাচন
                    </x-filament::button>
                    <x-filament::button size="sm" color="gray" wire:click="deselectAll">
                        সব বাতিল
                    </x-filament::button>
                    @if(count($selectedMembers) > 0)
                        <x-filament::button size="sm" color="success" wire:click="printCards" icon="heroicon-o-printer">
                            {{ count($selectedMembers) }} টি কার্ড প্রিন্ট
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Member Cards Grid --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($members as $member)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 border-2 {{ in_array($member->id, $selectedMembers) ? 'border-green-500' : 'border-transparent' }}">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-xl font-bold text-blue-600">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold">{{ $member->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $member->member_id }}</p>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $member->member_type === 'student' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ \App\Models\LibraryMember::memberTypeOptions()[$member->member_type] ?? $member->member_type }}
                            </span>
                        </div>
                        <div>
                            <input type="checkbox" class="rounded border-gray-300 w-5 h-5" value="{{ $member->id }}"
                                wire:model.live="selectedMembers">
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t dark:border-gray-700 flex justify-between items-center">
                        <div class="text-xs text-gray-500">
                            মেয়াদ: {{ $member->expiry_date?->format('d M Y') ?? 'সীমাহীন' }}
                        </div>
                        <x-filament::button size="xs" color="info" wire:click="printSingleCard({{ $member->id }})"
                            icon="heroicon-o-printer">
                            প্রিন্ট
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>

        @if($members->isEmpty())
            <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
                <x-heroicon-o-users class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                <p class="text-gray-500">কোন সদস্য পাওয়া যায়নি</p>
            </div>
        @endif
    @endif
</x-filament-panels::page>