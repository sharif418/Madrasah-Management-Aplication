<?php

namespace Database\Seeders;

use App\Models\ExamType;
use Illuminate\Database\Seeder;

class ExamTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * মাদরাসার জন্য সাধারণ পরীক্ষার ধরণ সেট করা
     */
    public function run(): void
    {
        $examTypes = [
            [
                'name' => 'প্রথম সাময়িক',
                'name_en' => 'First Term',
                'percentage' => 20,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'দ্বিতীয় সাময়িক',
                'name_en' => 'Second Term',
                'percentage' => 20,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'অর্ধ-বার্ষিক',
                'name_en' => 'Half Yearly',
                'percentage' => 30,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'বার্ষিক',
                'name_en' => 'Annual',
                'percentage' => 30,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'টেস্ট পরীক্ষা',
                'name_en' => 'Test Exam',
                'percentage' => 0,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'মডেল টেস্ট',
                'name_en' => 'Model Test',
                'percentage' => 0,
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'ক্লাস টেস্ট',
                'name_en' => 'Class Test',
                'percentage' => 0,
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'মাসিক পরীক্ষা',
                'name_en' => 'Monthly Exam',
                'percentage' => 0,
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($examTypes as $examType) {
            ExamType::updateOrCreate(
                ['name' => $examType['name']],
                $examType
            );
        }
    }
}
