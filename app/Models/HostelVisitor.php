<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelVisitor extends Model
{
    protected $fillable = [
        'hostel_id',
        'student_id',
        'visitor_name',
        'visitor_phone',
        'visitor_nid',
        'relation',
        'purpose',
        'check_in',
        'check_out',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    // Relation options
    public static function relationOptions(): array
    {
        return [
            'father' => 'পিতা',
            'mother' => 'মাতা',
            'brother' => 'ভাই',
            'sister' => 'বোন',
            'uncle' => 'চাচা/মামা',
            'aunt' => 'চাচী/মামী',
            'guardian' => 'অভিভাবক',
            'other' => 'অন্যান্য',
        ];
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('check_in', today());
    }

    public function scopeCurrentlyInside($query)
    {
        return $query->whereNotNull('check_in')->whereNull('check_out');
    }

    public function scopeCheckedOut($query)
    {
        return $query->whereNotNull('check_out');
    }

    // Helper
    public function getStatusAttribute(): string
    {
        if ($this->check_out) {
            return 'বের হয়েছেন';
        }
        return 'ভিতরে আছেন';
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->check_in)
            return null;

        $end = $this->check_out ?? now();
        return $this->check_in->diffForHumans($end, true);
    }
}
