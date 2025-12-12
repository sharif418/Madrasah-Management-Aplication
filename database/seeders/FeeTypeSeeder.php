<?php

namespace Database\Seeders;

use App\Models\FeeType;
use Illuminate\Database\Seeder;

class FeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * মাদরাসার জন্য সাধারণ ফি টাইপ সেট করা
     */
    public function run(): void
    {
        $feeTypes = [
            [
                'name' => 'ভর্তি ফি',
                'code' => 'ADM',
                'description' => 'নতুন ছাত্র ভর্তির সময় একবার প্রদেয়',
                'is_recurring' => false,
                'is_active' => true,
            ],
            [
                'name' => 'মাসিক বেতন',
                'code' => 'TUI',
                'description' => 'প্রতি মাসে প্রদেয় শিক্ষা বেতন',
                'is_recurring' => true,
                'is_active' => true,
            ],
            [
                'name' => 'পরীক্ষা ফি',
                'code' => 'EXAM',
                'description' => 'প্রতিটি পরীক্ষার জন্য প্রদেয়',
                'is_recurring' => false,
                'is_active' => true,
            ],
            [
                'name' => 'সেশন চার্জ',
                'code' => 'SES',
                'description' => 'বার্ষিক সেশন ফি',
                'is_recurring' => false,
                'is_active' => true,
            ],
            [
                'name' => 'লাইব্রেরি ফি',
                'code' => 'LIB',
                'description' => 'লাইব্রেরি ব্যবহারের জন্য বার্ষিক ফি',
                'is_recurring' => false,
                'is_active' => true,
            ],
            [
                'name' => 'ল্যাব ফি',
                'code' => 'LAB',
                'description' => 'ল্যাবরেটরি ব্যবহারের জন্য',
                'is_recurring' => false,
                'is_active' => true,
            ],
            [
                'name' => 'পরিবহন ফি',
                'code' => 'TRN',
                'description' => 'মাদরাসা পরিবহন সেবার জন্য মাসিক ফি',
                'is_recurring' => true,
                'is_active' => true,
            ],
            [
                'name' => 'হোস্টেল ফি',
                'code' => 'HOS',
                'description' => 'আবাসিক ছাত্রদের জন্য মাসিক ফি',
                'is_recurring' => true,
                'is_active' => true,
            ],
            [
                'name' => 'খাবার ফি',
                'code' => 'FOOD',
                'description' => 'আবাসিক ছাত্রদের জন্য মাসিক খাবার খরচ',
                'is_recurring' => true,
                'is_active' => true,
            ],
            [
                'name' => 'ইউনিফর্ম',
                'code' => 'UNI',
                'description' => 'ইউনিফর্ম ক্রয়ের জন্য',
                'is_recurring' => false,
                'is_active' => true,
            ],
            [
                'name' => 'বই-খাতা',
                'code' => 'BOOK',
                'description' => 'পাঠ্যপুস্তক ও খাতার জন্য',
                'is_recurring' => false,
                'is_active' => true,
            ],
            [
                'name' => 'উন্নয়ন ফি',
                'code' => 'DEV',
                'description' => 'প্রতিষ্ঠান উন্নয়নের জন্য বার্ষিক ফি',
                'is_recurring' => false,
                'is_active' => true,
            ],
            [
                'name' => 'সার্টিফিকেট ফি',
                'code' => 'CERT',
                'description' => 'সার্টিফিকেট/প্রশংসাপত্র ইস্যুর জন্য',
                'is_recurring' => false,
                'is_active' => true,
            ],
            [
                'name' => 'অন্যান্য',
                'code' => 'OTHER',
                'description' => 'অন্যান্য বিবিধ ফি',
                'is_recurring' => false,
                'is_active' => true,
            ],
        ];

        foreach ($feeTypes as $feeType) {
            FeeType::updateOrCreate(
                ['code' => $feeType['code']],
                $feeType
            );
        }
    }
}
