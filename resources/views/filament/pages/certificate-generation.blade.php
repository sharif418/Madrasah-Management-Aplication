<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        <form wire:submit="generateCertificate">
            {{ $this->form }}

            <div class="mt-4 flex gap-3">
                <x-filament::button type="submit" icon="heroicon-o-document-check">
                    সার্টিফিকেট প্রস্তুত
                </x-filament::button>

                @if($certificateData)
                    <x-filament::button type="button" wire:click="downloadPdf" color="success"
                        icon="heroicon-o-arrow-down-tray">
                        PDF ডাউনলোড
                    </x-filament::button>
                @endif
            </div>
        </form>
    </div>

    @if($showPreview && $certificateData)
        {{-- Certificate Preview --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-xl border-4 border-double border-primary-600 dark:border-primary-400 shadow-lg p-8 max-w-3xl mx-auto">
            {{-- Header --}}
            <div class="text-center border-b-2 border-primary-200 dark:border-primary-700 pb-6 mb-6">
                <div
                    class="w-20 h-20 mx-auto bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mb-4">
                    <x-heroicon-o-academic-cap class="w-12 h-12 text-primary-600 dark:text-primary-400" />
                </div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">
                    {{ institution_name() ?? 'প্রতিষ্ঠানের নাম' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ institution_address() ?? '' }}</p>
            </div>

            {{-- Certificate Title --}}
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-primary-700 dark:text-primary-400 mb-2">
                    {{ $certificateData['certificate_type_name'] }}
                </h2>
                <div class="w-32 h-1 bg-primary-500 mx-auto rounded"></div>
            </div>

            {{-- Certificate Number --}}
            <div class="text-right mb-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    সার্টিফিকেট নং: <strong>{{ $certificateData['certificate_no'] }}</strong>
                </span>
            </div>

            {{-- Student Info Box --}}
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1 text-gray-600 dark:text-gray-400 w-1/4">নাম:</td>
                        <td class="py-1 font-semibold text-gray-800 dark:text-white">{{ $certificateData['student']->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600 dark:text-gray-400">পিতার নাম:</td>
                        <td class="py-1 font-semibold text-gray-800 dark:text-white">
                            {{ $certificateData['student']->father_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600 dark:text-gray-400">শ্রেণি:</td>
                        <td class="py-1 font-semibold text-gray-800 dark:text-white">
                            {{ $certificateData['student']->class?->name ?? '-' }}
                            @if($certificateData['student']->section)
                                ({{ $certificateData['student']->section->name }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 text-gray-600 dark:text-gray-400">আইডি নং:</td>
                        <td class="py-1 font-semibold text-gray-800 dark:text-white">
                            {{ $certificateData['student']->student_id ?? $certificateData['student']->admission_no }}</td>
                    </tr>
                </table>
            </div>

            {{-- Certificate Content --}}
            <div class="text-justify text-gray-700 dark:text-gray-300 leading-relaxed mb-6 px-4">
                {!! nl2br(e($certificateData['content'])) !!}
            </div>

            {{-- Extra Text --}}
            @if($certificateData['extra_text'])
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 p-4 mb-6">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        {!! nl2br(e($certificateData['extra_text'])) !!}
                    </p>
                </div>
            @endif

            {{-- Date --}}
            <div class="mb-8 text-gray-600 dark:text-gray-400">
                <span class="font-medium">ইস্যু তারিখ:</span>
                {{ $certificateData['issue_date']->format('d M, Y') }}
            </div>

            {{-- Signature Section --}}
            <div class="flex justify-between pt-8 border-t border-gray-200 dark:border-gray-600">
                <div class="text-center">
                    <div class="h-12"></div>
                    <div class="border-t border-gray-400 dark:border-gray-500 pt-2 px-8">
                        <p class="text-sm text-gray-600 dark:text-gray-400">শ্রেণি শিক্ষক</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="h-12"></div>
                    <div class="border-t border-gray-400 dark:border-gray-500 pt-2 px-8">
                        <p class="text-sm text-gray-600 dark:text-gray-400">প্রধান শিক্ষক</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="h-12"></div>
                    <div class="border-t border-gray-400 dark:border-gray-500 pt-2 px-8">
                        <p class="text-sm text-gray-600 dark:text-gray-400">অধ্যক্ষ</p>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <x-heroicon-o-document-check class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">সার্টিফিকেট তৈরি করুন</h3>
            <p class="text-gray-500 dark:text-gray-400">সার্টিফিকেটের ধরন ও ছাত্র নির্বাচন করে "সার্টিফিকেট প্রস্তুত" ক্লিক
                করুন।</p>
            <div class="mt-4 flex justify-center gap-2 flex-wrap">
                <span
                    class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm">চারিত্রিক</span>
                <span
                    class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm">মেধা</span>
                <span
                    class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-sm">অংশগ্রহণ</span>
                <span
                    class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-full text-sm">সমাপনী</span>
                <span
                    class="px-3 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 rounded-full text-sm">আচরণ</span>
            </div>
        </div>
    @endif
</x-filament-panels::page>