<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button wire:click="promote" size="lg">
                নিবর্াচিত ছাত্রদের প্রমোশন দিন (Promote Selected)
            </x-filament::button>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>