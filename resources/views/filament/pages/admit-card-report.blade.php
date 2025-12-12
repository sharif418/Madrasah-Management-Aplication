<x-filament-panels::page>
    <div class="max-w-4xl mx-auto bg-white p-8 rounded shadow">
        <form wire:submit="generate" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit" size="lg" icon="heroicon-o-printer">
                    প্রবেশপত্র ডাউনলোড করুন (Download PDF)
                </x-filament::button>
            </div>
        </form>
    </div>

    <div class="mt-8 text-sm text-gray-500">
        <h3 class="font-bold">নির্দেশনা:</h3>
        <ul class="list-disc ml-5">
            <li>প্রথমে পরীক্ষা এবং ক্লাস নির্বাচন করুন।</li>
            <li>যদি নির্দিষ্ট শাখার প্রবেশপত্র চান, তবে শাখা নির্বাচন করুন।</li>
            <li>'ডাউনলোড' বাটনে ক্লিক করলে সকল ছাত্রের প্রবেশপত্র একটি PDF ফাইলে জেনারেট হবে।</li>
        </ul>
    </div>
</x-filament-panels::page>