<?php

namespace App\Filament\Pages;

use App\Models\StudentFee;
use App\Models\Student;
use App\Models\ClassName;
use App\Models\SmsLog;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class FeeReminder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'ফি ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'ফি রিমাইন্ডার';

    protected static ?string $title = 'ফি বকেয়া SMS রিমাইন্ডার';

    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.pages.fee-reminder';

    public ?array $data = [];
    public Collection $dueStudents;
    public array $selectedStudents = [];
    public bool $showStudents = false;
    public int $smsCount = 0;

    // SMS Templates
    public array $templates = [
        'default' => 'প্রিয় অভিভাবক, আপনার সন্তান {student_name} ({class}) এর {month} মাসের ফি ৳{amount} বকেয়া আছে। অনুগ্রহ করে শীঘ্রই পরিশোধ করুন। - {institution}',
        'urgent' => 'জরুরি! {student_name} ({class}) এর ফি বকেয়া ৳{amount}। অনুগ্রহ করে আজই পরিশোধ করুন। যোগাযোগ: {phone}',
        'reminder' => 'স্মারক: {student_name} এর {month} মাসের বেতন বকেয়া আছে। পরিমাণ: ৳{amount}। - {institution}',
    ];

    public function mount(): void
    {
        $this->dueStudents = collect();
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
            'template' => 'default',
            'min_due' => 0,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ফিল্টার')
                    ->schema([
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                                    ->placeholder('সকল শ্রেণি')
                                    ->native(false),

                                Forms\Components\Select::make('month')
                                    ->label('মাস')
                                    ->options([
                                        1 => 'জানুয়ারি',
                                        2 => 'ফেব্রুয়ারি',
                                        3 => 'মার্চ',
                                        4 => 'এপ্রিল',
                                        5 => 'মে',
                                        6 => 'জুন',
                                        7 => 'জুলাই',
                                        8 => 'আগস্ট',
                                        9 => 'সেপ্টেম্বর',
                                        10 => 'অক্টোবর',
                                        11 => 'নভেম্বর',
                                        12 => 'ডিসেম্বর',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('year')
                                    ->label('বছর')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('min_due')
                                    ->label('সর্বনিম্ন বকেয়া (৳)')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('এই পরিমাণের বেশি বকেয়া থাকলে'),

                                Forms\Components\Select::make('template')
                                    ->label('SMS টেমপ্লেট')
                                    ->options([
                                        'default' => 'সাধারণ রিমাইন্ডার',
                                        'urgent' => 'জরুরি রিমাইন্ডার',
                                        'reminder' => 'সংক্ষিপ্ত স্মারক',
                                    ])
                                    ->default('default')
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('SMS প্রিভিউ')
                    ->schema([
                        Forms\Components\Textarea::make('custom_message')
                            ->label('কাস্টম মেসেজ (ঐচ্ছিক)')
                            ->helperText('ভেরিয়েবল: {student_name}, {class}, {month}, {amount}, {institution}, {phone}')
                            ->rows(3)
                            ->placeholder('খালি রাখলে সিলেক্টেড টেমপ্লেট ব্যবহার হবে'),
                    ])
                    ->visible(fn() => $this->showStudents),
            ])
            ->statePath('data');
    }

    public function loadStudents(): void
    {
        $this->form->validate();
        $data = $this->data;

        $query = StudentFee::query()
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->where('due_amount', '>', $data['min_due'] ?? 0)
            ->whereIn('status', ['pending', 'partial'])
            ->whereHas('student', fn($q) => $q->where('status', 'active'));

        if (!empty($data['class_id'])) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $data['class_id']));
        }

        $this->dueStudents = $query->with(['student.class', 'student.guardian'])->get()
            ->groupBy('student_id')
            ->map(function ($fees) {
                $student = $fees->first()->student;
                return [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'class' => $student->class->name ?? '-',
                    'guardian_name' => $student->guardian->name ?? '-',
                    'phone' => $student->guardian->phone ?? $student->phone ?? null,
                    'total_due' => $fees->sum('due_amount'),
                    'fee_count' => $fees->count(),
                ];
            })
            ->filter(fn($s) => !empty($s['phone']));

        $this->selectedStudents = [];
        $this->showStudents = true;

        Notification::make()
            ->success()
            ->title($this->dueStudents->count() . ' জন ছাত্রের অভিভাবক পাওয়া গেছে')
            ->send();
    }

    public function selectAll(): void
    {
        $this->selectedStudents = $this->dueStudents->pluck('student_id')->toArray();
    }

    public function deselectAll(): void
    {
        $this->selectedStudents = [];
    }

    public function sendSms(): void
    {
        if (empty($this->selectedStudents)) {
            Notification::make()->warning()->title('কাউকে নির্বাচন করুন')->send();
            return;
        }

        // Check if SMS is enabled
        if (!config('services.sms.enabled', false)) {
            Notification::make()
                ->warning()
                ->title('SMS সার্ভিস নিষ্ক্রিয়')
                ->body('সেটিংস থেকে SMS সার্ভিস চালু করুন')
                ->send();
            return;
        }

        $data = $this->data;
        $template = $data['custom_message'] ?: ($this->templates[$data['template']] ?? $this->templates['default']);
        $months = [1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল', 5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট', 9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর'];

        $sent = 0;
        $failed = 0;

        foreach ($this->selectedStudents as $studentId) {
            $studentData = $this->dueStudents->firstWhere('student_id', $studentId);
            if (!$studentData || !$studentData['phone'])
                continue;

            $message = str_replace([
                '{student_name}',
                '{class}',
                '{month}',
                '{amount}',
                '{institution}',
                '{phone}',
            ], [
                $studentData['student_name'],
                $studentData['class'],
                $months[$data['month']] ?? $data['month'],
                number_format($studentData['total_due'], 0),
                institution_name() ?? 'মাদরাসা',
                institution_phone() ?? '',
            ], $template);

            // Log SMS (actual sending would use SMS gateway)
            try {
                // Here you would integrate with actual SMS gateway
                // For now, we just log it
                if (class_exists(SmsLog::class)) {
                    SmsLog::create([
                        'phone' => $studentData['phone'],
                        'message' => $message,
                        'type' => 'fee_reminder',
                        'status' => 'pending',
                        'student_id' => $studentId,
                    ]);
                }

                $sent++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        $this->smsCount = $sent;

        Notification::make()
            ->success()
            ->title('SMS রিমাইন্ডার পাঠানো হয়েছে!')
            ->body("$sent টি SMS কিউতে যোগ হয়েছে। ব্যর্থ: $failed টি")
            ->send();

        $this->selectedStudents = [];
    }
}
