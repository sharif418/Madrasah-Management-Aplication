<x-filament-panels::page>
    <form wire:submit.prevent="saveAttendance">
        {{ $this->form }}

        <div class="mt-4 flex gap-2">
            <x-filament::button type="button" wire:click="loadStudents" color="primary" icon="heroicon-o-arrow-path">
                ছাত্র লোড করুন
            </x-filament::button>
        </div>

        @if($showStudents && $students->count() > 0)
            <div class="mt-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                    <!-- Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    ছাত্র তালিকা ({{ $students->count() }} জন)
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    প্রতিটি ছাত্রের জন্য উপস্থিতি নির্বাচন করুন
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <x-filament::button size="sm" color="success" wire:click="markAll('present')"
                                    icon="heroicon-o-check">
                                    সকলকে উপস্থিত
                                </x-filament::button>
                                <x-filament::button size="sm" color="danger" wire:click="markAll('absent')"
                                    icon="heroicon-o-x-mark">
                                    সকলকে অনুপস্থিত
                                </x-filament::button>
                                <x-filament::button size="sm" color="gray" wire:click="toggleTimeEntry"
                                    icon="heroicon-o-clock">
                                    {{ $enableTimeEntry ? 'সময় বন্ধ' : 'সময় যোগ' }}
                                </x-filament::button>
                                @if($enableTimeEntry)
                                    <x-filament::button size="sm" color="info" wire:click="setDefaultTime"
                                        icon="heroicon-o-clock">
                                        বর্তমান সময় সেট
                                    </x-filament::button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Student List -->
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $index => $student)
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors
                                        {{ $attendanceData[$student->id] === 'absent' ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}
                                        {{ $attendanceData[$student->id] === 'late' ? 'bg-yellow-50/50 dark:bg-yellow-900/10' : '' }}"
                                wire:key="student-{{ $student->id }}">

                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    {{-- Student Info --}}
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 flex items-center justify-center rounded-full 
                                                    {{ $attendanceData[$student->id] === 'present' ? 'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : '' }}
                                                    {{ $attendanceData[$student->id] === 'absent' ? 'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400' : '' }}
                                                    {{ $attendanceData[$student->id] === 'late' ? 'bg-amber-100 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400' : '' }}
                                                    {{ $attendanceData[$student->id] === 'leave' ? 'bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}
                                                    font-bold text-sm">
                                            {{ $student->roll_no ?? ($index + 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $student->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->student_id ?? $student->admission_no }} | পিতা:
                                                {{ $student->father_name }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Attendance Options --}}
                                    <div class="flex flex-wrap items-center gap-3">
                                        {{-- Status Radio Buttons --}}
                                        <div class="flex items-center gap-2">
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" wire:model.live="attendanceData.{{ $student->id }}"
                                                    value="present"
                                                    class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700">
                                                <span
                                                    class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">উপস্থিত</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" wire:model.live="attendanceData.{{ $student->id }}"
                                                    value="absent"
                                                    class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700">
                                                <span
                                                    class="text-sm text-red-600 dark:text-red-400 font-medium">অনুপস্থিত</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" wire:model.live="attendanceData.{{ $student->id }}"
                                                    value="late"
                                                    class="w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700">
                                                <span
                                                    class="text-sm text-amber-600 dark:text-amber-400 font-medium">বিলম্বে</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" wire:model.live="attendanceData.{{ $student->id }}"
                                                    value="leave"
                                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                                                <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">ছুটি</span>
                                            </label>
                                        </div>

                                        {{-- Time Input (when enabled) --}}
                                        @if($enableTimeEntry && $attendanceData[$student->id] !== 'absent')
                                            <div class="flex items-center gap-2">
                                                <input type="time" wire:model.blur="timeData.{{ $student->id }}.in_time"
                                                    class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                                    placeholder="প্রবেশ">
                                                <span class="text-gray-400">-</span>
                                                <input type="time" wire:model.blur="timeData.{{ $student->id }}.out_time"
                                                    class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                                    placeholder="প্রস্থান">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Footer with Summary -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex flex-wrap gap-4 text-sm">
                                <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                    উপস্থিত: {{ collect($attendanceData)->filter(fn($s) => $s === 'present')->count() }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400">
                                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                    অনুপস্থিত: {{ collect($attendanceData)->filter(fn($s) => $s === 'absent')->count() }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400">
                                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                    বিলম্বে: {{ collect($attendanceData)->filter(fn($s) => $s === 'late')->count() }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400">
                                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                    ছুটি: {{ collect($attendanceData)->filter(fn($s) => $s === 'leave')->count() }}
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <x-filament::button type="submit" color="success" size="lg" icon="heroicon-o-check-circle">
                                    সংরক্ষণ করুন
                                </x-filament::button>
                                @if(collect($attendanceData)->filter(fn($s) => $s === 'absent')->count() > 0)
                                    <x-filament::button type="button" wire:click="saveAndSendSms" color="warning" size="lg"
                                        icon="heroicon-o-device-phone-mobile">
                                        সংরক্ষণ ও SMS
                                    </x-filament::button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($showStudents && $students->count() === 0)
            <div class="mt-6 p-8 text-center bg-gray-50 dark:bg-gray-800 rounded-xl">
                <x-heroicon-o-user-group class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500" />
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">কোন ছাত্র পাওয়া যায়নি</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">এই শ্রেণি/শাখায় সক্রিয় ছাত্র নেই</p>
            </div>
        @endif
    </form>
</x-filament-panels::page>