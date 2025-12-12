<?php

namespace App\Filament\Pages;

use App\Models\LibraryMember;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;

class LibraryCardPrint extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'লাইব্রেরি';

    protected static ?string $navigationLabel = 'লাইব্রেরি কার্ড';

    protected static ?string $title = 'লাইব্রেরি কার্ড প্রিন্ট';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.library-card-print';

    public ?array $data = [];
    public Collection $members;
    public array $selectedMembers = [];
    public bool $showMembers = false;

    public function mount(): void
    {
        $this->members = collect();
        $this->form->fill([
            'member_type' => 'all',
            'status' => 'active',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('member_type')
                    ->label('সদস্যের ধরণ')
                    ->options([
                        'all' => 'সকল',
                        'student' => 'ছাত্র',
                        'teacher' => 'শিক্ষক',
                        'staff' => 'কর্মচারী',
                        'external' => 'বহিরাগত',
                    ])
                    ->default('all')
                    ->native(false),

                Forms\Components\Select::make('status')
                    ->label('স্ট্যাটাস')
                    ->options([
                        'active' => 'সক্রিয়',
                        'all' => 'সকল',
                    ])
                    ->default('active')
                    ->native(false),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function loadMembers(): void
    {
        $data = $this->data;

        $query = LibraryMember::query();

        if ($data['member_type'] !== 'all') {
            $query->where('member_type', $data['member_type']);
        }

        if ($data['status'] === 'active') {
            $query->where('status', 'active');
        }

        $this->members = $query->orderBy('name')->get();
        $this->selectedMembers = [];
        $this->showMembers = true;

        Notification::make()
            ->success()
            ->title($this->members->count() . ' জন সদস্য পাওয়া গেছে')
            ->send();
    }

    public function selectAll(): void
    {
        $this->selectedMembers = $this->members->pluck('id')->toArray();
    }

    public function deselectAll(): void
    {
        $this->selectedMembers = [];
    }

    public function printCards()
    {
        if (empty($this->selectedMembers)) {
            Notification::make()->warning()->title('কোন সদস্য নির্বাচন করা হয়নি')->send();
            return;
        }

        $members = $this->members->whereIn('id', $this->selectedMembers);

        $pdf = Pdf::loadView('pdf.library-card', [
            'members' => $members,
            'date' => now()->format('d/m/Y'),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'library_cards_' . date('Y-m-d') . '.pdf');
    }

    public function printSingleCard($memberId)
    {
        $member = LibraryMember::find($memberId);
        if (!$member)
            return;

        $pdf = Pdf::loadView('pdf.library-card', [
            'members' => collect([$member]),
            'date' => now()->format('d/m/Y'),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'library_card_' . $member->member_id . '.pdf');
    }
}
