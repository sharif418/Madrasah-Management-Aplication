<?php

namespace App\Filament\Pages;

use App\Models\HifzProgress;
use App\Models\Student;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Filament\Notifications\Notification;

class HafizCertificate extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'হিফজ ও কিতাব';

    protected static ?string $navigationLabel = 'হাফেজ সার্টিফিকেট';

    protected static ?string $title = 'হাফেজ সার্টিফিকেট জেনারেট';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.hafiz-certificate';

    public ?array $data = [];
    public $student = null;
    public array $progressSummary = [];
    public bool $showCertificate = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->label('ছাত্র নির্বাচন')
                    ->options(function () {
                        return Student::whereHas('hifzProgress', function ($q) {
                            $q->whereNotNull('sabaq_para');
                        })
                            ->with('class')
                            ->get()
                            ->mapWithKeys(fn($s) => [
                                $s->id => $s->name . ' (' . ($s->class?->name ?? '-') . ')'
                            ]);
                    })
                    ->searchable()
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('issue_date')
                    ->label('ইস্যু তারিখ')
                    ->default(now())
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('certificate_no')
                    ->label('সার্টিফিকেট নং')
                    ->default(fn() => 'HFZ-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT))
                    ->required(),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function preview(): void
    {
        $this->form->validate();

        $this->student = Student::with('class')->find($this->data['student_id']);

        // Get hifz summary
        $progress = HifzProgress::where('student_id', $this->data['student_id'])->get();

        $completedParas = $progress->whereNotNull('sabaq_para')
            ->pluck('sabaq_para')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $firstEntry = $progress->sortBy('date')->first();
        $lastEntry = $progress->sortByDesc('date')->first();

        $this->progressSummary = [
            'completed_paras_count' => count($completedParas),
            'completed_paras' => $completedParas,
            'start_date' => $firstEntry?->date?->format('d M Y'),
            'completion_date' => $lastEntry?->date?->format('d M Y'),
            'total_days' => $progress->unique('date')->count(),
            'is_complete' => count($completedParas) >= 30,
        ];

        $this->showCertificate = true;

        if (!$this->progressSummary['is_complete']) {
            Notification::make()
                ->warning()
                ->title('এই ছাত্রের হিফজ সম্পন্ন হয়নি')
                ->body('সম্পন্ন পারা: ' . count($completedParas) . '/30')
                ->send();
        }
    }

    public function printCertificate()
    {
        if (!$this->student) {
            $this->preview();
        }

        $pdf = Pdf::loadView('pdf.hafiz-certificate', [
            'student' => $this->student,
            'summary' => $this->progressSummary,
            'certificateNo' => $this->data['certificate_no'],
            'issueDate' => $this->data['issue_date'],
        ])->setPaper('a4', 'landscape');

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'hafiz_certificate_' . $this->student->id . '.pdf');
    }
}
