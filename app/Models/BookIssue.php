<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookIssue extends Model
{
    protected $fillable = [
        'book_id',
        'library_member_id',
        'issue_date',
        'due_date',
        'return_date',
        'fine_amount',
        'fine_paid',
        'status',
        'issued_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean',
    ];

    public const STATUS_ISSUED = 'issued';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_LOST = 'lost';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ISSUED => 'ইস্যু করা হয়েছে',
            self::STATUS_RETURNED => 'ফেরত দেওয়া হয়েছে',
            self::STATUS_OVERDUE => 'মেয়াদোত্তীর্ণ',
            self::STATUS_LOST => 'হারিয়ে গেছে',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function libraryMember(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', self::STATUS_ISSUED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_ISSUED)
            ->where('due_date', '<', now());
    }

    /**
     * Calculate fine based on overdue days
     * @param float $finePerDay Fine amount per day (default 5 taka)
     */
    public function calculateFine(float $finePerDay = 5.00): float
    {
        if ($this->status !== self::STATUS_ISSUED) {
            return (float) ($this->fine_amount ?? 0);
        }

        $dueDate = $this->due_date;
        $today = now();

        if ($dueDate && $today->gt($dueDate)) {
            $overdueDays = (int) $dueDate->diffInDays($today);
            return $overdueDays * $finePerDay;
        }

        return 0.0;
    }

    /**
     * Return the book
     */
    public function returnBook(): void
    {
        $this->return_date = now()->toDateString();
        $this->fine_amount = $this->calculateFine();
        $this->status = self::STATUS_RETURNED;
        $this->save();

        // Increase available copies
        $this->book?->increaseAvailableCopies();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($issue) {
            // Reduce available copies when book is issued
            $issue->book?->reduceAvailableCopies();
        });
    }
}
