<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alumni extends Model
{
    protected $table = 'alumni';

    protected $fillable = [
        'student_id',
        'name',
        'phone',
        'email',
        'passing_year',
        'last_class',
        'current_occupation',
        'current_address',
        'achievements',
        'photo',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('passing_year', $year);
    }
}
