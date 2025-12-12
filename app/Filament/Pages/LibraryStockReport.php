<?php

namespace App\Filament\Pages;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class LibraryStockReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'লাইব্রেরি';

    protected static ?string $navigationLabel = 'স্টক রিপোর্ট';

    protected static ?string $title = 'লাইব্রেরি স্টক রিপোর্ট';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.library-stock-report';

    public ?array $data = [];
    public array $summary = [];
    public array $categoryWise = [];
    public array $recentActivity = [];
    public bool $showReport = false;

    public function mount(): void
    {
        $this->form->fill([
            'category_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('ক্যাটাগরি')
                    ->options(BookCategory::pluck('name', 'id'))
                    ->placeholder('সকল ক্যাটাগরি')
                    ->native(false),
            ])
            ->columns(1)
            ->statePath('data');
    }

    public function generate(): void
    {
        $categoryId = $this->data['category_id'] ?? null;

        $query = Book::query();
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->with('category')->get();

        // Summary
        $this->summary = [
            'total_books' => $books->count(),
            'total_copies' => $books->sum('total_copies'),
            'available_copies' => $books->sum('available_copies'),
            'issued_copies' => $books->sum('total_copies') - $books->sum('available_copies'),
            'total_value' => $books->sum(fn($b) => $b->price * $b->total_copies),
            'categories' => $books->pluck('category_id')->unique()->count(),
        ];

        // Category-wise summary
        $this->categoryWise = BookCategory::withCount(['books'])
            ->with([
                'books' => function ($q) {
                    $q->select('id', 'category_id', 'total_copies', 'available_copies', 'price');
                }
            ])
            ->get()
            ->map(function ($cat) {
                return [
                    'name' => $cat->name,
                    'book_count' => $cat->books_count,
                    'total_copies' => $cat->books->sum('total_copies'),
                    'available' => $cat->books->sum('available_copies'),
                    'issued' => $cat->books->sum('total_copies') - $cat->books->sum('available_copies'),
                    'value' => $cat->books->sum(fn($b) => $b->price * $b->total_copies),
                ];
            })
            ->toArray();

        // Recent activity (last 10 issues/returns)
        $this->recentActivity = BookIssue::with(['book', 'member'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($issue) => [
                'date' => $issue->updated_at->format('d M Y'),
                'book' => $issue->book?->title ?? '-',
                'member' => $issue->member?->name ?? '-',
                'action' => $issue->status === 'issued' ? 'জারি' : 'ফেরত',
                'status' => $issue->status,
            ])
            ->toArray();

        $this->showReport = true;
    }

    public function exportPdf()
    {
        if (!$this->showReport) {
            $this->generate();
        }

        $pdf = Pdf::loadView('pdf.library-stock', [
            'summary' => $this->summary,
            'categoryWise' => $this->categoryWise,
            'date' => now()->format('d/m/Y'),
        ]);

        return Response::streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'library_stock_' . date('Y-m-d') . '.pdf');
    }
}
