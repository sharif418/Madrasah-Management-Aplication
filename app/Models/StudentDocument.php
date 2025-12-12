<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'title',
        'file_path',
        'description',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get document type label in Bengali
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'birth_certificate' => 'জন্ম সনদ',
            'previous_certificate' => 'পূর্ববর্তী সার্টিফিকেট',
            'photo' => 'ছবি',
            'nid' => 'জাতীয় পরিচয়পত্র',
            'other' => 'অন্যান্য',
            default => $this->type,
        };
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
