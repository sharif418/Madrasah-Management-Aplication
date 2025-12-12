<x-filament-panels::page>
    <div class="space-y-6">
        @php $children = $this->getChildren(); @endphp
        
        @if($children->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                <x-heroicon-o-user-plus class="w-16 h-16 mx-auto mb-4 text-gray-400"/>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">কোন সন্তান যুক্ত নেই</h3>
                <p class="text-gray-500 mt-2">অনুগ্রহ করে প্রশাসনের সাথে যোগাযোগ করুন।</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Children Selector -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white">সন্তান নির্বাচন করুন</h3>
                        </div>
                        <div class="p-2">
                            @foreach($children as $child)
                                <button 
                                    wire:click="selectChild({{ $child->id }})"
                                    class="w-full p-3 rounded-lg text-left transition-colors {{ $selectedChildId === $child->id ? 'bg-teal-50 dark:bg-teal-900/30 border-2 border-teal-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-teal-100 dark:bg-teal-900 flex items-center justify-center text-teal-600 dark:text-teal-400 font-bold">
                                            {{ mb_substr($child->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $child->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $child->class->name ?? '' }}</div>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Child Details -->
                <div class="lg:col-span-3">
                    @php $child = $this->getSelectedChild(); @endphp
                    @if($child)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                            <!-- Header -->
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-teal-50 to-cyan-50 dark:from-teal-900/20 dark:to-cyan-900/20 rounded-t-xl">
                                <div class="flex items-center gap-6">
                                    <div class="w-20 h-20 rounded-full bg-teal-100 dark:bg-teal-900 flex items-center justify-center text-teal-600 dark:text-teal-400 text-3xl font-bold">
                                        {{ mb_substr($child->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $child->name }}</h2>
                                        <p class="text-gray-500">ভর্তি নং: {{ $child->admission_no }}</p>
                                        <div class="flex gap-2 mt-2">
                                            <span class="px-3 py-1 bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300 text-sm rounded-full">
                                                {{ $child->class->name ?? 'N/A' }}
                                            </span>
                                            @if($child->section)
                                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-sm rounded-full">
                                                    {{ $child->section->name }}
                                                </span>
                                            @endif
                                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-full">
                                                রোল: {{ $child->roll_no ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Personal Info -->
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <x-heroicon-o-user class="w-5 h-5 text-teal-500"/>
                                        ব্যক্তিগত তথ্য
                                    </h4>
                                    <dl class="space-y-3">
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">জন্ম তারিখ</dt>
                                            <dd class="font-medium text-gray-900 dark:text-white">{{ $child->date_of_birth?->format('d M Y') ?? 'N/A' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">লিঙ্গ</dt>
                                            <dd class="font-medium text-gray-900 dark:text-white">{{ $child->gender === 'male' ? 'ছেলে' : 'মেয়ে' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">রক্তের গ্রুপ</dt>
                                            <dd class="font-medium text-gray-900 dark:text-white">{{ $child->blood_group ?? 'N/A' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">ভর্তির তারিখ</dt>
                                            <dd class="font-medium text-gray-900 dark:text-white">{{ $child->admission_date?->format('d M Y') ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Guardian Info -->
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <x-heroicon-o-home class="w-5 h-5 text-teal-500"/>
                                        পিতামাতার তথ্য
                                    </h4>
                                    <dl class="space-y-3">
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">পিতার নাম</dt>
                                            <dd class="font-medium text-gray-900 dark:text-white">{{ $child->father_name }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">পিতার মোবাইল</dt>
                                            <dd class="font-medium text-gray-900 dark:text-white">{{ $child->father_phone ?? 'N/A' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">মাতার নাম</dt>
                                            <dd class="font-medium text-gray-900 dark:text-white">{{ $child->mother_name }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">মাতার মোবাইল</dt>
                                            <dd class="font-medium text-gray-900 dark:text-white">{{ $child->mother_phone ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <x-heroicon-o-map-pin class="w-5 h-5 text-teal-500"/>
                                        ঠিকানা
                                    </h4>
                                    <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                                        {{ $child->present_address }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
