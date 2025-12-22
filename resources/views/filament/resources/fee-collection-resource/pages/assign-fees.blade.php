<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="assignFees">
            {{ $this->form }}

            <div class="mt-4 flex gap-4">
                <x-filament::button type="button" wire:click="loadStudents" color="primary">
                    ছাত্র লোড করুন
                </x-filament::button>
            </div>

            @if($showStudents && $students->count() > 0)
                <div class="mt-6">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
                        <!-- Header -->
                        <div
                            class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-gray-800 dark:to-gray-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                ছাত্র তালিকা - মোট {{ $students->count() }} জন
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                নিচের সকল ছাত্রকে নির্বাচিত ফি এসাইন করা হবে। প্রয়োজনে আলাদা ছাড় দিন।
                            </p>
                        </div>

                        <!-- Students Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            রোল
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            নাম
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            ভর্তি নং
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            পিতার নাম
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                            ছাড় (ঐচ্ছিক)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($students as $student)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->roll_no ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $student->name }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->admission_no }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->father_name }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <select
                                                    wire:change="setStudentDiscount({{ $student->id }}, $event.target.value)"
                                                    class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                                    <option value="">ডিফল্ট ছাড়</option>
                                                    @foreach($this->getDiscountOptions() as $id => $name)
                                                        <option value="{{ $id }}" {{ ($studentDiscounts[$student->id] ?? null) == $id ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">টিপস:</span>
                                    উপরে "সকলের জন্য ছাড়" সিলেক্ট করলে সেটি সবার জন্য প্রযোজ্য হবে। এখানে আলাদা ছাড় দিলে
                                    সেটি ওভাররাইড হবে।
                                </p>
                                <x-filament::button type="submit" color="success" size="lg">
                                    সকলকে ফি এসাইন করুন
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($showStudents && $students->count() === 0)
                <div class="mt-6 p-8 text-center bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <x-heroicon-o-user-group class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">কোন ছাত্র পাওয়া যায়নি</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">এই শ্রেণিতে সক্রিয় ছাত্র নেই</p>
                </div>
            @endif
        </form>
    </div>
</x-filament-panels::page>