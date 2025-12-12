<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationGroup = 'ছাত্র ব্যবস্থাপনা';

    protected static ?string $navigationLabel = 'বাল্ক ইম্পোর্ট';

    protected static ?string $title = 'ছাত্র ইম্পোর্ট (Excel)';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.student-import';

    public ?array $data = [];

    public bool $showResults = false;
    public int $successCount = 0;
    public int $skipCount = 0;
    public array $importErrors = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ফাইল আপলোড')
                    ->description('Excel ফাইল (.xlsx, .csv) আপলোড করুন। স্যাম্পল ফাইল ডাউনলোড করে সেই ফরম্যাটে ডাটা পূরণ করুন।')
                    ->schema([
                        Forms\Components\FileUpload::make('file')
                            ->label('Excel ফাইল')
                            ->disk('local')
                            ->directory('imports')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'text/csv',
                                'application/vnd.ms-excel'
                            ])
                            ->required()
                            ->helperText('সর্বোচ্চ ফাইল সাইজ: 5MB'),
                    ]),
            ])
            ->statePath('data');
    }

    public function import()
    {
        $data = $this->form->getState();
        $filePath = storage_path('app/' . $data['file']);

        if (!file_exists($filePath)) {
            Notification::make()
                ->title('ফাইল পাওয়া যায়নি')
                ->danger()
                ->send();
            return;
        }

        try {
            $import = new StudentsImport();
            Excel::import($import, $filePath);

            // Get import summary
            $summary = $import->getSummary();

            $this->successCount = $summary['success'];
            $this->skipCount = $summary['skipped'];
            $this->importErrors = $summary['errors'];
            $this->showResults = true;

            // Show notification
            if ($this->successCount > 0) {
                Notification::make()
                    ->title('ইম্পোর্ট সম্পন্ন!')
                    ->body("{$this->successCount} জন ছাত্র সফলভাবে ইম্পোর্ট হয়েছে।")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('কোন ছাত্র ইম্পোর্ট হয়নি')
                    ->body('অনুগ্রহ করে ফাইল চেক করুন এবং নিচের ত্রুটিগুলো দেখুন।')
                    ->warning()
                    ->send();
            }

            // Clear form
            $this->form->fill();

            // Delete uploaded file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('ইম্পোর্ট ব্যর্থ হয়েছে')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadSample(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $headers = [
                'name',           // ছাত্রের নাম (আবশ্যক)
                'name_en',        // ইংরেজি নাম
                'gender',         // লিঙ্গ (male/female)
                'dob',            // জন্ম তারিখ (YYYY-MM-DD)
                'class',          // শ্রেণী নাম
                'section',        // শাখা নাম
                'student_id',     // ছাত্র আইডি (ঐচ্ছিক)
                'phone',          // অভিভাবকের ফোন
                'father_name',    // পিতার নাম
                'mother_name',    // মাতার নাম
                'guardian_name',  // অভিভাবকের নাম
                'address',        // ঠিকানা
                'blood_group',    // রক্তের গ্রুপ
                'religion',       // ধর্ম
                'birth_certificate', // জন্ম সনদ নম্বর
                'previous_school',   // পূর্বের স্কুল
            ];

            $sampleData = [
                [
                    'মোহাম্মদ আব্দুল্লাহ',
                    'Mohammad Abdullah',
                    'male',
                    '2010-05-15',
                    'প্রথম শ্রেণী',
                    'ক শাখা',
                    '',
                    '01712345678',
                    'মোহাম্মদ করিম',
                    'ফাতেমা বেগম',
                    'মোহাম্মদ করিম',
                    'ঢাকা, বাংলাদেশ',
                    'B+',
                    'ইসলাম',
                    '20101234567890123',
                    '',
                ],
                [
                    'ফাতেমা খাতুন',
                    'Fatema Khatun',
                    'female',
                    '2011-08-20',
                    'দ্বিতীয় শ্রেণী',
                    'খ শাখা',
                    '',
                    '01812345678',
                    'আব্দুল রহমান',
                    'সালমা বেগম',
                    'আব্দুল রহমান',
                    'চট্টগ্রাম, বাংলাদেশ',
                    'A+',
                    'ইসলাম',
                    '20111234567890456',
                    '',
                ],
            ];

            // Output CSV
            $output = fopen('php://output', 'w');

            // BOM for UTF-8 Excel compatibility
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($output, $headers);

            // Sample data
            foreach ($sampleData as $row) {
                fputcsv($output, $row);
            }

            fclose($output);

        }, 'student_import_sample.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function resetResults(): void
    {
        $this->showResults = false;
        $this->successCount = 0;
        $this->skipCount = 0;
        $this->importErrors = [];
    }
}
