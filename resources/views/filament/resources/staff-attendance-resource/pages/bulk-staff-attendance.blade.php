<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex gap-4 justify-end">
            <x-filament::button type="button" color="gray" wire:click="loadAll">
                <x-heroicon-o-users class="w-5 h-5 mr-2" />
                সবাই লোড করুন
            </x-filament::button>

            <x-filament::button type="submit" color="success">
                <x-heroicon-o-check class="w-5 h-5 mr-2" />
                হাজিরা সেভ করুন
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>