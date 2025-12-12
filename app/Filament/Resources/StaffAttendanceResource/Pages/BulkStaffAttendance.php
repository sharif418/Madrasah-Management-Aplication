<?php

namespace App\Filament\Resources\StaffAttendanceResource\Pages;

use App\Filament\Resources\StaffAttendanceResource;
use App\Models\StaffAttendance;
use App\Models\Teacher;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class BulkStaffAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = StaffAttendanceResource::class;

    protected static string $view = 'filament.resources.staff-attendance-resource.pages.bulk-staff-attendance';

    protected static ?string $title = 'বাল্ক স্টাফ হাজিরা';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date' => now()->toDateString(),
            'attendee_type' => 'teacher',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('বাল্ক হাজিরা')
                    ->description('একসাথে সকল শিক্ষক/কর্মচারীর হাজিরা দিন')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->label('তারিখ')
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('attendee_type')
                                    ->label('ধরণ')
                                    ->options(StaffAttendance::attendeeTypeOptions())
                                    ->required()
                                    ->live()
                                    ->native(false),
                            ]),

                        Forms\Components\Repeater::make('attendances')
                            ->label('হাজিরা তালিকা')
                            ->schema([
                                Forms\Components\Select::make('attendee_id')
                                    ->label('নাম')
                                    ->options(function (Get $get) {
                                        $type = $get('../../attendee_type');
                                        if ($type === 'teacher') {
                                            return Teacher::where('status', 'active')->pluck('name', 'id');
                                        }
                                        return Staff::where('status', 'active')->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->native(false)
                                    ->searchable()
                                    ->columnSpan(2),

                                Forms\Components\Select::make('status')
                                    ->label('স্ট্যাটাস')
                                    ->options(StaffAttendance::statusOptions())
                                    ->default('present')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TimePicker::make('check_in')
                                    ->label('ইন')
                                    ->native(false),

                                Forms\Components\TimePicker::make('check_out')
                                    ->label('আউট')
                                    ->native(false),
                            ])
                            ->columns(5)
                            ->addActionLabel('আরেকজন যোগ করুন')
                            ->defaultItems(0)
                            ->reorderable(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $saved = 0;
        $skipped = 0;

        foreach ($data['attendances'] as $attendance) {
            // Check if already exists
            $exists = StaffAttendance::where('attendee_type', $data['attendee_type'])
                ->where('attendee_id', $attendance['attendee_id'])
                ->whereDate('date', $data['date'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            StaffAttendance::create([
                'attendee_type' => $data['attendee_type'],
                'attendee_id' => $attendance['attendee_id'],
                'date' => $data['date'],
                'status' => $attendance['status'],
                'check_in' => $attendance['check_in'],
                'check_out' => $attendance['check_out'],
                'marked_by' => Auth::id(),
            ]);

            $saved++;
        }

        if ($saved > 0) {
            Notification::make()
                ->success()
                ->title('হাজিরা সেভ হয়েছে!')
                ->body("{$saved} জনের হাজিরা সংরক্ষিত হয়েছে।" . ($skipped > 0 ? " {$skipped} জন আগে থেকে ছিল।" : ''))
                ->send();
        } else {
            Notification::make()
                ->warning()
                ->title('কোন হাজিরা সেভ হয়নি')
                ->body('সকলের হাজিরা আগে থেকে আছে অথবা কেউ যোগ করা হয়নি।')
                ->send();
        }

        $this->redirect(StaffAttendanceResource::getUrl('index'));
    }

    public function loadAll(): void
    {
        $data = $this->form->getState();
        $type = $data['attendee_type'];

        $attendees = $type === 'teacher'
            ? Teacher::where('status', 'active')->get()
            : Staff::where('status', 'active')->get();

        $attendances = [];
        foreach ($attendees as $attendee) {
            $attendances[] = [
                'attendee_id' => $attendee->id,
                'status' => 'present',
                'check_in' => '09:00',
                'check_out' => null,
            ];
        }

        $this->data['attendances'] = $attendances;

        Notification::make()
            ->info()
            ->title('সবাই লোড হয়েছে')
            ->body(count($attendances) . ' জন যোগ করা হয়েছে।')
            ->send();
    }
}
