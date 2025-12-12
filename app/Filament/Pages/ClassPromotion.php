<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\ClassName;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms\Components\actions\Action as FormAction;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClassPromotion extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationLabel = 'ক্লাস প্রমোশন';

    protected static ?string $title = 'ক্লাস প্রমোশন (Class Promotion)';

    protected static ?string $navigationGroup = 'একাডেমিক সেটআপ';

    protected static ?string $slug = 'class-promotion';

    protected static string $view = 'filament.pages.class-promotion';

    public ?array $data = [];

    public function mount(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $this->form->fill([
            'source_academic_year_id' => $currentYear?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormSection::make('প্রমোশন তথ্য')
                    ->schema([
                        // Source
                        FormSection::make('বর্তমান ক্লাস')
                            ->schema([
                                Select::make('source_academic_year_id')
                                    ->label('বর্তমান শিক্ষাবর্ষ')
                                    ->options(AcademicYear::all()->pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn() => $this->resetTable()),
                                Select::make('source_class_id')
                                    ->label('বর্তমান ক্লাস')
                                    ->options(ClassName::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('source_section_id', null);
                                        $this->resetTable();
                                    }),
                                Select::make('source_section_id')
                                    ->label('বর্তমান শাখা')
                                    ->options(fn(Get $get) => Section::where('class_id', $get('source_class_id'))->pluck('name', 'id'))
                                    ->live()
                                    ->afterStateUpdated(fn() => $this->resetTable()),
                            ])->columns(3),

                        // Target
                        FormSection::make('পরবর্তী ক্লাস (টার্গেট)')
                            ->schema([
                                Select::make('target_academic_year_id')
                                    ->label('পরবর্তী শিক্ষাবর্ষ')
                                    ->options(AcademicYear::all()->pluck('name', 'id'))
                                    ->required(),
                                Select::make('target_class_id')
                                    ->label('পরবর্তী ক্লাস')
                                    ->options(ClassName::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('target_section_id', null);
                                    }),
                                Select::make('target_section_id')
                                    ->label('পরবর্তী শাখা')
                                    ->options(fn(Get $get) => Section::where('class_id', $get('target_class_id'))->pluck('name', 'id')),
                            ])->columns(3),
                    ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $sourceYearId = $this->data['source_academic_year_id'] ?? null;
                $sourceClassId = $this->data['source_class_id'] ?? null;
                $sourceSectionId = $this->data['source_section_id'] ?? null;

                if (!$sourceYearId || !$sourceClassId) {
                    return Student::query()->whereRaw('1 = 0'); // Empty query
                }

                return Student::query()
                    ->where('academic_year_id', $sourceYearId)
                    ->where('class_id', $sourceClassId)
                    ->when($sourceSectionId, fn($q) => $q->where('section_id', $sourceSectionId))
                    ->where('status', 'active');
            })
            ->columns([
                TextColumn::make('roll_no')
                    ->label('রোল')
                    ->sortable(),
                TextColumn::make('admission_no')
                    ->label('ভর্তি নং')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('নাম')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('father_name')
                    ->label('পিতার নাম'),
                TextColumn::make('status')
                    ->label('স্ট্যাটাস')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        default => 'gray',
                    }),
            ])
            ->emptyStateHeading('কোনো ছাত্র পাওয়া যায়নি')
            ->emptyStateDescription('অনুগ্রহ করে উপরের ফর্ম থেকে শিক্ষাবর্ষ এবং ক্লাস নির্বাচন করুন।')
            ->selectable(); // Enable checkboxes
    }

    public function promote(): void
    {
        $selectedRecords = $this->getSelectedTableRecords();

        if ($selectedRecords->count() === 0) {
            Notification::make()
                ->title('কোনো ছাত্র নির্বাচন করা হয়নি')
                ->warning()
                ->send();
            return;
        }

        $this->form->validate();

        $targetYearId = $this->data['target_academic_year_id'];
        $targetClassId = $this->data['target_class_id'];
        $targetSectionId = $this->data['target_section_id'];

        if ($this->data['source_academic_year_id'] == $targetYearId && $this->data['source_class_id'] == $targetClassId) {
            Notification::make()
                ->title('বর্তমান এবং পরবর্তী ক্লাস একই হতে পারে না')
                ->warning()
                ->send();
            return;
        }


        DB::transaction(function () use ($selectedRecords, $targetYearId, $targetClassId, $targetSectionId) {
            foreach ($selectedRecords as $student) {
                $oldYearId = $student->academic_year_id;
                $oldClassId = $student->class_id;
                $oldSectionId = $student->section_id;

                // 1. Update Student Table (Current Status)
                $student->update([
                    'academic_year_id' => $targetYearId,
                    'class_id' => $targetClassId,
                    'section_id' => $targetSectionId,
                    // Roll no isn't reset here, usually handled separately or left as is until updated manually
                ]);

                // 2. Create New Enrollment Record
                Enrollment::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $targetYearId,
                    'class_id' => $targetClassId,
                    'section_id' => $targetSectionId,
                    'roll_no' => $student->roll_no,
                    'enrollment_date' => now(), // Or start of academic year
                    'status' => 'active',
                ]);

                // 3. Update Old Enrollment Record (if exists)
                if ($oldYearId) {
                    Enrollment::where('student_id', $student->id)
                        ->where('academic_year_id', $oldYearId)
                        ->update(['status' => 'promoted']);
                }
            }
        });

        Notification::make()
            ->title('সফলভাবে প্রমোশন করা হয়েছে')
            ->success()
            ->send();

        $this->resetTable();
        // Clear selection
        $this->replaceMountedTableAction(null);
        $this->form->fill([
            'source_academic_year_id' => $this->data['source_academic_year_id'],
            'source_class_id' => $this->data['source_class_id'],
            'source_section_id' => $this->data['source_section_id'],
            'target_academic_year_id' => $targetYearId, // Keep target to ease next batch
            'target_class_id' => $targetClassId,
            'target_section_id' => null,
        ]);
    }


}
