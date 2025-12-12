<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'category',
        'description',
        'download_count',
        'is_published',
    ];

    protected $casts = [
        'download_count' => 'integer',
        'is_published' => 'boolean',
    ];

    public static function categoryOptions(): array
    {
        return [
            'form' => 'ফর্ম',
            'syllabus' => 'সিলেবাস',
            'result' => 'ফলাফল',
            'notice' => 'নোটিশ',
            'other' => 'অন্যান্য',
        ];
    }

    public static function fileTypeOptions(): array
    {
        return [
            'pdf' => 'PDF',
            'doc' => 'Word',
            'xls' => 'Excel',
            'image' => 'ছবি',
            'other' => 'অন্যান্য',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Increment download count
     */
    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }
}
