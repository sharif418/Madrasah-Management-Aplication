<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\ClassName;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use App\Filament\Pages\BasePage;

class CertificateGeneration extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'সার্টিফিকেট';

    protected static ?string $title = 'সার্টিফিকেট প্রস্তুত';

    protected static ?string $navigationGroup = 'পরীক্ষা ব্যবস্থাপনা';

    protected static ?int $navigationSort = 14;

    protected static string $view = 'filament.pages.certificate-generation';

    public ?array $data = [];
    public ?array $certificateData = null;
    public bool $showPreview = false;

    protected array $certificateTypes = [
        'character' => 'চারিত্রিক সনদপত্র',
        'merit' => 'মেধা সনদপত্র',
        'participation' => 'অংশগ্রহণ সনদপত্র',
        'completion' => 'সমাপনী সনদপত্র',
        'conduct' => 'আচরণ সনদপত্র',
    ];

    public function mount(): void
    {
        $this->form->fill([
            'certificate_type' => 'character',
            'issue_date' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('certificate_type')
                    ->label('সার্টিফিকেটের ধরন')
                    ->options($this->certificateTypes)
                    ->required()
                    ->native(false)
                    ->live(),

                Select::make('class_id')
                    ->label('শ্রেণি')
                    ->options(ClassName::where('is_active', true)->orderBy('order')->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn() => $this->data['student_id'] = null),

                Select::make('section_id')
                    ->label('শাখা (ঐচ্ছিক)')
                    ->options(fn(Get $get) => Section::where('class_id', $get('class_id'))->pluck('name', 'id'))
                    ->native(false)
                    ->live(),

                Select::make('student_id')
                    ->label('ছাত্র')
                    ->options(function (Get $get) {
                        $classId = $get('class_id');
                        $sectionId = $get('section_id');
                        if (!$classId)
                            return [];

                        $query = Student::where('class_id', $classId)->where('status', 'active');
                        if ($sectionId) {
                            $query->where('section_id', $sectionId);
                        }
                        return $query->orderBy('roll_no')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->native(false),

                DatePicker::make('issue_date')
                    ->label('ইস্যু তারিখ')
                    ->required()
                    ->default(now())
                    ->native(false),

                TextInput::make('certificate_no')
                    ->label('সার্টিফিকেট নং (ঐচ্ছিক)')
                    ->placeholder('স্বয়ংক্রিয়')
                    ->helperText('খালি রাখলে স্বয়ংক্রিয় নম্বর দেওয়া হবে'),

                Textarea::make('extra_text')
                    ->label('অতিরিক্ত টেক্সট (ঐচ্ছিক)')
                    ->placeholder('সার্টিফিকেটে অতিরিক্ত উল্লেখ করতে চাইলে লিখুন...')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function generateCertificate(): void
    {
        $this->form->validate();

        $studentId = $this->data['student_id'];
        $certificateType = $this->data['certificate_type'];
        $issueDate = $this->data['issue_date'];
        $certificateNo = $this->data['certificate_no'] ?? ('CERT-' . date('Ymd') . '-' . $studentId);
        $extraText = $this->data['extra_text'] ?? '';

        $student = Student::with(['class', 'section'])->find($studentId);

        if (!$student) {
            Notification::make()->title('ছাত্র পাওয়া যায়নি')->danger()->send();
            return;
        }

        // Get certificate content based on type
        $content = $this->getCertificateContent($certificateType, $student);

        $this->certificateData = [
            'student' => $student,
            'certificate_type' => $certificateType,
            'certificate_type_name' => $this->certificateTypes[$certificateType] ?? '',
            'certificate_no' => $certificateNo,
            'issue_date' => \Carbon\Carbon::parse($issueDate),
            'content' => $content,
            'extra_text' => $extraText,
            'generated_at' => now(),
        ];

        $this->showPreview = true;

        Notification::make()->title('সার্টিফিকেট প্রস্তুত হয়েছে')->success()->send();
    }

    protected function getCertificateContent(string $type, Student $student): string
    {
        $name = $student->name;
        $fatherName = $student->father_name ?? '-';
        $className = $student->class?->name ?? '-';
        $instituteName = institution_name() ?? 'প্রতিষ্ঠান';

        $templates = [
            'character' => "এই মর্মে সনদপত্র প্রদান করা যাচ্ছে যে, {$name}, পিতা- {$fatherName}, এই প্রতিষ্ঠানের {$className} শ্রেণির একজন নিয়মিত ছাত্র/ছাত্রী। সে অধ্যয়নকালে সৎ, নিষ্ঠাবান ও চরিত্রবান হিসেবে পরিচিত ছিল। আমার জানামতে তার বিরুদ্ধে কোন প্রকার অভিযোগ নেই।\n\nআমি তার জীবনের সর্বাঙ্গীণ সাফল্য কামনা করি।",

            'merit' => "এই মর্মে সনদপত্র প্রদান করা যাচ্ছে যে, {$name}, পিতা- {$fatherName}, {$instituteName} এর {$className} শ্রেণিতে অধ্যয়নকালে কৃতিত্বপূর্ণ ফলাফল অর্জন করেছে।\n\nতার এই অসাধারণ মেধা ও পরিশ্রমের স্বীকৃতিস্বরূপ এই মেধা সনদপত্র প্রদান করা হলো।",

            'participation' => "এই মর্মে প্রত্যয়ন করা যাচ্ছে যে, {$name}, পিতা- {$fatherName}, {$instituteName} এর {$className} শ্রেণির ছাত্র/ছাত্রী, সফলভাবে আয়োজিত অনুষ্ঠান/প্রতিযোগিতায় অংশগ্রহণ করেছে।\n\nতার এই অংশগ্রহণের স্বীকৃতিস্বরূপ এই সনদপত্র প্রদান করা হলো।",

            'completion' => "এই মর্মে প্রত্যয়ন করা যাচ্ছে যে, {$name}, পিতা- {$fatherName}, {$instituteName} এ অধ্যয়ন করে সফলভাবে {$className} শ্রেণি সমাপ্ত করেছে।\n\nতার এই সাফল্যের স্বীকৃতিস্বরূপ এই সমাপনী সনদপত্র প্রদান করা হলো।",

            'conduct' => "এই মর্মে সনদপত্র প্রদান করা যাচ্ছে যে, {$name}, পিতা- {$fatherName}, এই প্রতিষ্ঠানের {$className} শ্রেণির ছাত্র/ছাত্রী। সে অধ্যয়নকালে সুশৃঙ্খল, ভদ্র ও নিয়মানুবর্তী ছিল। তার আচার-আচরণ সন্তোষজনক।\n\nআমি তার ভবিষ্যৎ জীবনে সাফল্য কামনা করি।",
        ];

        return $templates[$type] ?? $templates['character'];
    }

    public function downloadPdf()
    {
        if (!$this->certificateData) {
            Notification::make()->title('প্রথমে সার্টিফিকেট তৈরি করুন')->warning()->send();
            return;
        }

        $data = [
            'certificateData' => $this->certificateData,
            'institute' => [
                'name' => institution_name(),
                'address' => institution_address(),
                'phone' => institution_phone(),
                'logo' => institution_logo(),
            ],
        ];

        $pdf = Pdf::loadView('pdf.certificate', $data)
            ->setPaper('a4', 'portrait');

        $fileName = 'certificate-' . $this->certificateData['student']->name . '-' . now()->timestamp . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }
}
