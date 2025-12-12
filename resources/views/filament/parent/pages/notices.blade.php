<x-filament-panels::page>
    <div class="space-y-6">
        @php $notices = $this->getNotices(); @endphp

        @if($notices->isEmpty())
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-12 text-center">
                <x-heroicon-o-megaphone class="w-16 h-16 mx-auto mb-4 text-gray-400"/>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">কোন নোটিশ নেই</h3>
                <p class="text-gray-500 mt-2">বর্তমানে কোন নোটিশ প্রকাশিত হয়নি।</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($notices as $notice)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-4 md:p-6">
                            <div class="flex items-start gap-4">
                                <div class="p-3 rounded-lg {{ match($notice->type ?? 'general') {
                                    'urgent' => 'bg-red-100 dark:bg-red-900/30',
                                    'exam' => 'bg-purple-100 dark:bg-purple-900/30',
                                    'holiday' => 'bg-green-100 dark:bg-green-900/30',
                                    'event' => 'bg-blue-100 dark:bg-blue-900/30',
                                    default => 'bg-gray-100 dark:bg-gray-700',
                                } }}">
                                    @switch($notice->type ?? 'general')
                                        @case('urgent')
                                            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400"/>
                                            @break
                                        @case('exam')
                                            <x-heroicon-o-academic-cap class="w-6 h-6 text-purple-600 dark:text-purple-400"/>
                                            @break
                                        @case('holiday')
                                            <x-heroicon-o-calendar class="w-6 h-6 text-green-600 dark:text-green-400"/>
                                            @break
                                        @case('event')
                                            <x-heroicon-o-sparkles class="w-6 h-6 text-blue-600 dark:text-blue-400"/>
                                            @break
                                        @default
                                            <x-heroicon-o-megaphone class="w-6 h-6 text-gray-600 dark:text-gray-400"/>
                                    @endswitch
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $notice->title }}
                                        </h3>
                                        <span class="text-sm text-gray-500">
                                            {{ $notice->publish_date?->format('d M Y') ?? $notice->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    
                                    @if($notice->type ?? null)
                                        <span class="inline-block px-2 py-1 text-xs rounded-full mb-3 {{ match($notice->type) {
                                            'urgent' => 'bg-red-100 text-red-700',
                                            'exam' => 'bg-purple-100 text-purple-700',
                                            'holiday' => 'bg-green-100 text-green-700',
                                            'event' => 'bg-blue-100 text-blue-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        } }}">
                                            {{ match($notice->type) {
                                                'urgent' => 'জরুরি',
                                                'exam' => 'পরীক্ষা',
                                                'holiday' => 'ছুটি',
                                                'event' => 'অনুষ্ঠান',
                                                default => 'সাধারণ',
                                            } }}
                                        </span>
                                    @endif
                                    
                                    <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300">
                                        {!! nl2br(e($notice->content ?? $notice->description ?? '')) !!}
                                    </div>
                                    
                                    @if($notice->attachment)
                                        <a href="{{ asset('storage/' . $notice->attachment) }}" target="_blank" 
                                           class="inline-flex items-center gap-2 mt-4 text-teal-600 hover:text-teal-700">
                                            <x-heroicon-o-paper-clip class="w-4 h-4"/>
                                            সংযুক্তি ডাউনলোড করুন
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
