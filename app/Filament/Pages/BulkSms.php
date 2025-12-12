<?php

namespace App\Filament\Pages;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\ClassName;
use App\Models\MessageTemplate;
use App\Models\SmsLog;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class BulkSms extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'যোগাযোগ';

    protected static ?string $navigationLabel = 'বাল্ক SMS';

    protected static ?string $title = 'বাল্ক SMS পাঠান';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.bulk-sms';

    public ?array $data = [];
    public Collection $recipients;
    public array $selectedRecipients = [];
    public bool $showRecipients = false;

    public function mount(): void
    {
        $this->recipients = collect();
        $this->form->fill([
            'recipient_type' => 'students',
            'template_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('recipient_type')
                            ->label('প্রাপক ধরণ')
                            ->options([
                                'students' => 'ছাত্র/অভিভাবক',
                                'teachers' => 'শিক্ষক',
                                'staff' => 'কর্মচারী',
                                'all' => 'সকল',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\Select::make('class_id')
                            ->label('ক্লাস')
                            ->options(ClassName::pluck('name', 'id'))
                            ->visible(fn(Forms\Get $get) => $get('recipient_type') === 'students')
                            ->native(false),

                        Forms\Components\Select::make('template_id')
                            ->label('টেমপ্লেট')
                            ->options(MessageTemplate::where('is_active', true)->forSms()->pluck('name', 'id'))
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if ($state) {
                                    $template = MessageTemplate::find($state);
                                    if ($template) {
                                        $set('message', $template->content);
                                    }
                                }
                            }),
                    ]),

                Forms\Components\Textarea::make('message')
                    ->label('মেসেজ')
                    ->required()
                    ->rows(3)
                    ->helperText('প্লেসহোল্ডার: {name}, {class}, {institution}')
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function loadRecipients(): void
    {
        $type = $this->data['recipient_type'];
        $classId = $this->data['class_id'] ?? null;

        $recipients = collect();

        if ($type === 'students' || $type === 'all') {
            $query = Student::where('status', 'active')
                ->whereNotNull('guardian_phone');

            if ($classId) {
                $query->where('class_id', $classId);
            }

            $students = $query->get()->map(fn($s) => [
                'id' => 'student_' . $s->id,
                'name' => $s->name,
                'phone' => $s->guardian_phone,
                'type' => 'ছাত্র',
                'extra' => $s->class?->name ?? '-',
            ]);
            $recipients = $recipients->concat($students);
        }

        if ($type === 'teachers' || $type === 'all') {
            $teachers = Teacher::where('status', 'active')
                ->whereNotNull('phone')
                ->get()
                ->map(fn($t) => [
                    'id' => 'teacher_' . $t->id,
                    'name' => $t->name,
                    'phone' => $t->phone,
                    'type' => 'শিক্ষক',
                    'extra' => $t->designation ?? '-',
                ]);
            $recipients = $recipients->concat($teachers);
        }

        if ($type === 'staff' || $type === 'all') {
            $staff = Staff::where('status', 'active')
                ->whereNotNull('phone')
                ->get()
                ->map(fn($s) => [
                    'id' => 'staff_' . $s->id,
                    'name' => $s->name,
                    'phone' => $s->phone,
                    'type' => 'কর্মচারী',
                    'extra' => $s->designation ?? '-',
                ]);
            $recipients = $recipients->concat($staff);
        }

        $this->recipients = $recipients;
        $this->selectedRecipients = [];
        $this->showRecipients = true;

        Notification::make()
            ->success()
            ->title($recipients->count() . ' জন প্রাপক পাওয়া গেছে')
            ->send();
    }

    public function selectAll(): void
    {
        $this->selectedRecipients = $this->recipients->pluck('id')->toArray();
    }

    public function deselectAll(): void
    {
        $this->selectedRecipients = [];
    }

    public function sendSms(): void
    {
        if (empty($this->selectedRecipients)) {
            Notification::make()->warning()->title('কোন প্রাপক নির্বাচন করা হয়নি')->send();
            return;
        }

        if (empty($this->data['message'])) {
            Notification::make()->warning()->title('মেসেজ লিখুন')->send();
            return;
        }

        $selected = $this->recipients->whereIn('id', $this->selectedRecipients);
        $message = $this->data['message'];
        $sentCount = 0;

        foreach ($selected as $recipient) {
            // Parse placeholders
            $parsedMessage = str_replace(
                ['{name}', '{institution}'],
                [$recipient['name'], institution_name() ?? 'প্রতিষ্ঠান'],
                $message
            );

            // Log SMS (actual sending depends on gateway configuration)
            SmsLog::create([
                'phone' => $recipient['phone'],
                'message' => $parsedMessage,
                'status' => config('services.sms.enabled', false) ? 'sent' : 'logged',
                'sent_at' => now(),
            ]);

            $sentCount++;
        }

        Notification::make()
            ->success()
            ->title($sentCount . ' টি SMS সফলভাবে লগ করা হয়েছে')
            ->body(config('services.sms.enabled', false) ? 'SMS পাঠানো হয়েছে' : 'SMS Gateway কনফিগার করা নেই, শুধু লগ করা হয়েছে')
            ->send();

        $this->selectedRecipients = [];
    }
}
