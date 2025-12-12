<x-filament-panels::page>
    @php
        $data = $this->getStudentData();
    @endphp

    @if(empty($data))
        <div class="p-6 text-center bg-danger-50 dark:bg-danger-900/20 rounded-xl">
            <x-heroicon-o-exclamation-triangle class="w-16 h-16 mx-auto text-danger-500 mb-4" />
            <h3 class="text-lg font-semibold text-danger-700 dark:text-danger-400">ছাত্র প্রোফাইল পাওয়া যায়নি</h3>
            <p class="text-danger-600 dark:text-danger-400">আপনার একাউন্টের সাথে কোন ছাত্র প্রোফাইল সংযুক্ত নেই।</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Photo & Basic Info Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-primary-500 to-primary-600 p-6 text-center">
                    <div class="w-32 h-32 mx-auto rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                        @if($data['photo_url'])
                            <img src="{{ $data['photo_url'] }}" alt="{{ $data['name'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <x-heroicon-o-user class="w-16 h-16 text-gray-400" />
                            </div>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-white mt-4">{{ $data['name'] }}</h2>
                    @if($data['name_en'])
                        <p class="text-primary-100">{{ $data['name_en'] }}</p>
                    @endif
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">ভর্তি নম্বর</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $data['admission_no'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">রোল নম্বর</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $data['roll_no'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">শ্রেণি</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $data['class'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-500 dark:text-gray-400">শাখা</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $data['section'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-user class="w-5 h-5 text-primary-500" />
                        ব্যক্তিগত তথ্য
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">পিতার নাম</label>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['father_name'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">মাতার নাম</label>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['mother_name'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">জন্ম তারিখ</label>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['date_of_birth'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">লিঙ্গ</label>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['gender'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">রক্তের গ্রুপ</label>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['blood_group'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">ভর্তির তারিখ</label>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['admission_date'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                        <x-heroicon-o-phone class="w-5 h-5 text-primary-500" />
                        যোগাযোগ তথ্য
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">ফোন নম্বর</label>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['phone'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500 dark:text-gray-400">ইমেইল</label>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['email'] ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-500 dark:text-gray-400">বর্তমান ঠিকানা</label>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $data['present_address'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>