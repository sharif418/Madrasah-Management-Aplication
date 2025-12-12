<x-filament-panels::page>
    {{-- Instructions Section --}}
    <div
        class="mb-6 p-5 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-primary-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-primary-100 dark:bg-primary-900/50 rounded-lg">
                <x-heroicon-o-information-circle class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">ব্যবহার নির্দেশিকা</h3>
                <ul class="list-disc ml-5 text-sm space-y-2 text-gray-600 dark:text-gray-400">
                    <li><strong class="text-gray-800 dark:text-gray-200">আবশ্যক কলাম:</strong> <code
                            class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs">name</code> (ছাত্রের নাম)
                    </li>
                    <li><strong class="text-gray-800 dark:text-gray-200">প্রস্তাবিত কলাম:</strong> phone, class,
                        section, father_name, gender, dob</li>
                    <li><strong class="text-gray-800 dark:text-gray-200">ঐচ্ছিক কলাম:</strong> student_id, name_en,
                        mother_name, address, blood_group, religion</li>
                    <li>Class এবং Section এর বানান সফটওয়্যারের শ্রেণী/শাখার নামের সাথে মিলতে হবে।</li>
                    <li>অভিভাবকের জন্য অটোমেটিক একাউন্ট তৈরি হবে (ডিফল্ট পাসওয়ার্ড: <strong>12345678</strong>)</li>
                    <li>ডুপ্লিকেট student_id থাকলে সেই সারি স্কিপ হবে।</li>
                </ul>
            </div>
        </div>

        {{-- Download Sample Button --}}
        <div class="mt-5 pt-4 border-t border-primary-200 dark:border-gray-700">
            <button type="button" wire:click="downloadSample"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm font-medium">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                <span>স্যাম্পল CSV ফাইল ডাউনলোড করুন</span>
            </button>
        </div>
    </div>

    {{-- Import Results Section --}}
    @if($showResults)
        <div
            class="mb-6 p-5 rounded-xl border shadow-sm {{ $successCount > 0 ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-700' }}">
            <div class="flex items-center justify-between mb-4">
                <h3
                    class="text-lg font-semibold {{ $successCount > 0 ? 'text-green-800 dark:text-green-200' : 'text-yellow-800 dark:text-yellow-200' }}">
                    <x-heroicon-o-chart-bar class="w-5 h-5 inline mr-2" />
                    ইম্পোর্ট ফলাফল
                </h3>
                <button type="button" wire:click="resetResults"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div
                    class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $successCount }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">সফল ইম্পোর্ট</div>
                </div>
                <div
                    class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
                    <div class="text-3xl font-bold text-orange-500 dark:text-orange-400">{{ $skipCount }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">স্কিপ হয়েছে</div>
                </div>
            </div>

            @if(count($importErrors) > 0)
                <div class="mt-4">
                    <h4 class="font-medium text-red-700 dark:text-red-400 mb-2">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 inline mr-1" />
                        সমস্যাসমূহ ({{ count($importErrors) }})
                    </h4>
                    <div
                        class="max-h-48 overflow-y-auto bg-white dark:bg-gray-800 rounded-lg border border-red-200 dark:border-red-800 p-3">
                        <ul class="text-sm text-red-600 dark:text-red-400 space-y-1">
                            @foreach($importErrors as $error)
                                <li class="flex items-start gap-2">
                                    <x-heroicon-o-x-circle class="w-4 h-4 flex-shrink-0 mt-0.5" />
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Upload Form --}}
    <form wire:submit="import">
        {{ $this->form }}

        <div class="mt-6 flex justify-end gap-3">
            <x-filament::button type="submit" color="primary" size="lg">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                ইম্পোর্ট শুরু করুন
            </x-filament::button>
        </div>
    </form>

    {{-- Expected Columns Table --}}
    <div class="mt-8 p-5 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
            <x-heroicon-o-table-cells class="w-5 h-5 inline mr-2" />
            কলাম রেফারেন্স
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">কলাম নাম</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">বিবরণ</th>
                        <th class="px-3 py-2 text-center font-medium text-gray-700 dark:text-gray-300">আবশ্যক</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">উদাহরণ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="bg-green-50 dark:bg-green-900/10">
                        <td class="px-3 py-2 font-mono text-xs">name</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">ছাত্রের নাম (বাংলা)</td>
                        <td class="px-3 py-2 text-center"><span class="text-green-600 font-bold">✓</span></td>
                        <td class="px-3 py-2 text-gray-500">মোহাম্মদ আব্দুল্লাহ</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-mono text-xs">name_en</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">ইংরেজি নাম</td>
                        <td class="px-3 py-2 text-center text-gray-400">-</td>
                        <td class="px-3 py-2 text-gray-500">Mohammad Abdullah</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-mono text-xs">gender</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">লিঙ্গ</td>
                        <td class="px-3 py-2 text-center text-gray-400">-</td>
                        <td class="px-3 py-2 text-gray-500">male / female</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-mono text-xs">dob</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">জন্ম তারিখ</td>
                        <td class="px-3 py-2 text-center text-gray-400">-</td>
                        <td class="px-3 py-2 text-gray-500">2010-05-15</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-mono text-xs">class</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">শ্রেণী</td>
                        <td class="px-3 py-2 text-center text-gray-400">-</td>
                        <td class="px-3 py-2 text-gray-500">প্রথম শ্রেণী</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-mono text-xs">section</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">শাখা</td>
                        <td class="px-3 py-2 text-center text-gray-400">-</td>
                        <td class="px-3 py-2 text-gray-500">ক শাখা</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-mono text-xs">phone</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">অভিভাবকের ফোন</td>
                        <td class="px-3 py-2 text-center text-gray-400">-</td>
                        <td class="px-3 py-2 text-gray-500">01712345678</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-mono text-xs">father_name</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">পিতার নাম</td>
                        <td class="px-3 py-2 text-center text-gray-400">-</td>
                        <td class="px-3 py-2 text-gray-500">মোহাম্মদ করিম</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-mono text-xs">mother_name</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">মাতার নাম</td>
                        <td class="px-3 py-2 text-center text-gray-400">-</td>
                        <td class="px-3 py-2 text-gray-500">ফাতেমা বেগম</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>