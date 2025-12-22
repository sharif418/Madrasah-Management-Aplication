<?php

namespace App\Filament\Resources\FeeCollectionResource\Pages;

use App\Filament\Resources\FeeCollectionResource;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\FeeDiscount;
use App\Models\StudentFee;
use App\Models\AcademicYear;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class AssignFees extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = FeeCollectionResource::class;

    protected static string $view = 'filament.resources.fee-collection-resource.pages.assign-fees';

    protected static ?string $title = 'ফি এসাইন করুন';

    public ?array $data = [];
    public Collection $students;
    public bool $showStudents = false;
    public array $studentDiscounts = []; // Track individual student discounts

    public function mount(): void
    {
        $this->students = collect();
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ফি এসাইন সেটিংস')
                    ->description('ছাত্রদের ফি এসাইন করুন')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('class_id')
                                    ->label('শ্রেণি')
                                    ->options(\App\Models\ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->native(false),

                                Forms\Components\Select::make('fee_structure_id')
                                    ->label('ফি কাঠামো')
                                    ->options(function (Forms\Get $get) {
                                        $classId = $get('class_id');
                                        if (!$classId)
                                            return [];

                                        return FeeStructure::where('class_id', $classId)
                                            ->where('is_active', true)
                                            ->with('feeType')
                                            ->get()
                                            ->mapWithKeys(fn($structure) => [
                                                $structure->id => $structure->feeType->name . ' - ৳' . number_format((float) ($structure->amount ?? 0), 2)
                                            ]);
                                    })
                                    ->required()
                                    ->native(false)
                                    ->live(),

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
                                        12 => 'ডিসেম্বর'
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('year')
                                    ->label('বছর')
                                    ->numeric()
                                    ->required()
                                    ->default(now()->year),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('পরিশোধের শেষ তারিখ')
                                    ->native(false),

                                Forms\Components\Select::make('default_discount_id')
                                    ->label('সকলের জন্য ছাড় (ঐচ্ছিক)')
                                    ->options(FeeDiscount::where('is_active', true)->pluck('name', 'id'))
                                    ->placeholder('কোন ছাড় নেই')
                                    ->native(false)
                                    ->helperText('এই ছাড় সকল ছাত্রের জন্য প্রযোজ্য হবে। পৃথক ছাড়ের জন্য নিচে দেখুন।'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function loadStudents(): void
    {
        $data = $this->form->getState();

        if (empty($data['class_id']) || empty($data['fee_structure_id'])) {
            Notification::make()
                ->warning()
                ->title('শ্রেণি এবং ফি কাঠামো নির্বাচন করুন')
                ->send();
            return;
        }

        $this->students = Student::where('class_id', $data['class_id'])
            ->where('status', 'active')
            ->orderBy('roll_no')
            ->get();

        // Initialize individual discounts array
        $this->studentDiscounts = [];
        foreach ($this->students as $student) {
            $this->studentDiscounts[$student->id] = null;
        }

        $this->showStudents = true;

        Notification::make()
            ->success()
            ->title($this->students->count() . ' জন ছাত্র পাওয়া গেছে')
            ->send();
    }

    public function setStudentDiscount($studentId, $discountId): void
    {
        $this->studentDiscounts[$studentId] = $discountId ?: null;
    }

    public function assignFees(): void
    {
        $data = $this->form->getState();
        $feeStructure = FeeStructure::find($data['fee_structure_id']);

        if (!$feeStructure) {
            Notification::make()->danger()->title('ফি কাঠামো পাওয়া যায়নি')->send();
            return;
        }

        $defaultDiscountId = $data['default_discount_id'] ?? null;
        $defaultDiscount = $defaultDiscountId ? FeeDiscount::find($defaultDiscountId) : null;

        $created = 0;
        $skipped = 0;

        foreach ($this->students as $student) {
            // Check if already assigned
            $exists = StudentFee::where('student_id', $student->id)
                ->where('fee_structure_id', $data['fee_structure_id'])
                ->where('month', $data['month'])
                ->where('year', $data['year'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Get applicable discount (individual > default)
            $studentDiscountId = $this->studentDiscounts[$student->id] ?? $defaultDiscountId;
            $discount = $studentDiscountId ? FeeDiscount::find($studentDiscountId) : $defaultDiscount;

            // Calculate discount amount
            $originalAmount = $feeStructure->amount;
            $discountAmount = 0;

            if ($discount) {
                $discountAmount = $discount->calculateDiscount($originalAmount);
            }

            $finalAmount = max(0, $originalAmount - $discountAmount);

            StudentFee::create([
                'student_id' => $student->id,
                'fee_structure_id' => $data['fee_structure_id'],
                'fee_discount_id' => $discount?->id,
                'month' => $data['month'],
                'year' => $data['year'],
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'paid_amount' => 0,
                'due_amount' => $finalAmount,
                'status' => 'pending',
                'due_date' => $data['due_date'] ?? null,
            ]);

            $created++;
        }

        Notification::make()
            ->success()
            ->title('ফি এসাইন সম্পন্ন!')
            ->body("$created জন ছাত্রকে ফি এসাইন হয়েছে। $skipped জন আগে থেকে এসাইন ছিল।")
            ->send();

        $this->showStudents = false;
        $this->students = collect();
        $this->studentDiscounts = [];
    }

    public function getDiscountOptions(): array
    {
        return FeeDiscount::where('is_active', true)
            ->get()
            ->mapWithKeys(fn($d) => [$d->id => $d->name . ' (' . $d->formatted_discount . ')'])
            ->toArray();
    }
}
