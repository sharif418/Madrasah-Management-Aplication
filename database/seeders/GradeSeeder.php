<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * বাংলাদেশি গ্রেডিং সিস্টেম অনুযায়ী গ্রেড সেট করা
     */
    public function run(): void
    {
        $grades = [
            [
                'name' => 'A+',
                'min_marks' => 80,
                'max_marks' => 100,
                'grade_point' => 5.00,
                'remarks' => 'অত্যুত্তম',
                'is_active' => true,
            ],
            [
                'name' => 'A',
                'min_marks' => 70,
                'max_marks' => 79.99,
                'grade_point' => 4.00,
                'remarks' => 'উত্তম',
                'is_active' => true,
            ],
            [
                'name' => 'A-',
                'min_marks' => 60,
                'max_marks' => 69.99,
                'grade_point' => 3.50,
                'remarks' => 'ভালো',
                'is_active' => true,
            ],
            [
                'name' => 'B',
                'min_marks' => 50,
                'max_marks' => 59.99,
                'grade_point' => 3.00,
                'remarks' => 'সন্তোষজনক',
                'is_active' => true,
            ],
            [
                'name' => 'C',
                'min_marks' => 40,
                'max_marks' => 49.99,
                'grade_point' => 2.00,
                'remarks' => 'গ্রহণযোগ্য',
                'is_active' => true,
            ],
            [
                'name' => 'D',
                'min_marks' => 33,
                'max_marks' => 39.99,
                'grade_point' => 1.00,
                'remarks' => 'পাস',
                'is_active' => true,
            ],
            [
                'name' => 'F',
                'min_marks' => 0,
                'max_marks' => 32.99,
                'grade_point' => 0.00,
                'remarks' => 'ফেল',
                'is_active' => true,
            ],
        ];

        foreach ($grades as $grade) {
            Grade::updateOrCreate(
                ['name' => $grade['name']],
                $grade
            );
        }
    }
}
