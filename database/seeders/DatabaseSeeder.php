<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Designation;
use App\Models\ClassName;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Shift;
use App\Models\Grade;
use App\Models\FeeType;
use App\Models\LeaveType;
use App\Models\BookCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $this->createRoles();

        // Create Super Admin
        $this->createSuperAdmin();

        // Create Academic Data
        $this->createAcademicData();

        // Create Settings
        $this->createInitialSettings();
    }

    private function createRoles(): void
    {
        $roles = [
            'super_admin' => 'সুপার এডমিন',
            'principal' => 'মুহতামিম',
            'academic_admin' => 'একাডেমিক এডমিন',
            'accounts_admin' => 'হিসাব এডমিন',
            'teacher' => 'শিক্ষক',
            'student' => 'ছাত্র',
            'parent' => 'অভিভাবক',
            'librarian' => 'লাইব্রেরিয়ান',
            'hostel_warden' => 'হোস্টেল ওয়ার্ডেন',
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }

    private function createSuperAdmin(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@madrasah.com'],
            [
                'name' => 'সুপার এডমিন',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $admin->assignRole('super_admin');
    }

    private function createAcademicData(): void
    {
        // Academic Year
        $currentYear = date('Y');
        AcademicYear::firstOrCreate(
            ['name' => $currentYear . '-' . ($currentYear + 1)],
            [
                'start_date' => $currentYear . '-01-01',
                'end_date' => ($currentYear + 1) . '-12-31',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        // Departments
        $departments = [
            ['name' => 'হিফজ বিভাগ', 'name_en' => 'Hifz Department', 'order' => 1],
            ['name' => 'নাজেরা বিভাগ', 'name_en' => 'Nazera Department', 'order' => 2],
            ['name' => 'কিতাব বিভাগ', 'name_en' => 'Kitab Department', 'order' => 3],
            ['name' => 'জেনারেল বিভাগ', 'name_en' => 'General Department', 'order' => 4],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }

        // Designations
        $designations = [
            ['name' => 'মুহতামিম', 'name_en' => 'Principal', 'order' => 1],
            ['name' => 'নায়েবে মুহতামিম', 'name_en' => 'Vice Principal', 'order' => 2],
            ['name' => 'শায়খুল হাদিস', 'name_en' => 'Shaykhul Hadith', 'order' => 3],
            ['name' => 'সিনিয়র উস্তাদ', 'name_en' => 'Senior Teacher', 'order' => 4],
            ['name' => 'উস্তাদ', 'name_en' => 'Teacher', 'order' => 5],
            ['name' => 'জুনিয়র উস্তাদ', 'name_en' => 'Junior Teacher', 'order' => 6],
            ['name' => 'হাফেজ সাহেব', 'name_en' => 'Hafez', 'order' => 7],
            ['name' => 'অফিস সহকারী', 'name_en' => 'Office Assistant', 'order' => 8],
        ];

        foreach ($designations as $desig) {
            Designation::firstOrCreate(['name' => $desig['name']], $desig);
        }

        // Classes
        $hifzDept = Department::where('name', 'হিফজ বিভাগ')->first();
        $kitabDept = Department::where('name', 'কিতাব বিভাগ')->first();
        $nazeraDept = Department::where('name', 'নাজেরা বিভাগ')->first();

        $classes = [
            // Hifz
            ['name' => 'হিফজুল কুরআন', 'department_id' => $hifzDept?->id, 'order' => 1],
            // Nazera
            ['name' => 'নূরানী', 'department_id' => $nazeraDept?->id, 'order' => 2],
            ['name' => 'নাজেরা', 'department_id' => $nazeraDept?->id, 'order' => 3],
            // Kitab
            ['name' => 'ইবতেদায়ী', 'numeric_name' => '1', 'department_id' => $kitabDept?->id, 'order' => 4],
            ['name' => 'মুতাওয়াসসিতা', 'numeric_name' => '2', 'department_id' => $kitabDept?->id, 'order' => 5],
            ['name' => 'সানাবিয়্যা উলা', 'numeric_name' => '3', 'department_id' => $kitabDept?->id, 'order' => 6],
            ['name' => 'সানাবিয়্যা সানিয়া', 'numeric_name' => '4', 'department_id' => $kitabDept?->id, 'order' => 7],
            ['name' => 'ফজিলত ১ম বর্ষ', 'numeric_name' => '5', 'department_id' => $kitabDept?->id, 'order' => 8],
            ['name' => 'ফজিলত ২য় বর্ষ', 'numeric_name' => '6', 'department_id' => $kitabDept?->id, 'order' => 9],
            ['name' => 'তাকমীল (দাওরা হাদিস)', 'numeric_name' => '7', 'department_id' => $kitabDept?->id, 'order' => 10],
        ];

        foreach ($classes as $class) {
            $created = ClassName::firstOrCreate(['name' => $class['name']], $class);

            // Create default sections
            Section::firstOrCreate(
                ['class_id' => $created->id, 'name' => 'ক শাখা'],
                ['capacity' => 40, 'order' => 1]
            );
        }

        // Subjects
        $subjects = [
            ['name' => 'কুরআন মাজিদ', 'name_en' => 'Quran', 'code' => 'QUR'],
            ['name' => 'হাদিস শরীফ', 'name_en' => 'Hadith', 'code' => 'HAD'],
            ['name' => 'ফিকহ', 'name_en' => 'Fiqh', 'code' => 'FIQ'],
            ['name' => 'আরবি সাহিত্য', 'name_en' => 'Arabic Literature', 'code' => 'ARB'],
            ['name' => 'আরবি ব্যাকরণ', 'name_en' => 'Arabic Grammar', 'code' => 'ARG'],
            ['name' => 'বাংলা', 'name_en' => 'Bangla', 'code' => 'BAN'],
            ['name' => 'ইংরেজি', 'name_en' => 'English', 'code' => 'ENG'],
            ['name' => 'গণিত', 'name_en' => 'Mathematics', 'code' => 'MAT'],
            ['name' => 'ইসলামের ইতিহাস', 'name_en' => 'Islamic History', 'code' => 'HIS'],
            ['name' => 'আকাইদ', 'name_en' => 'Aqeedah', 'code' => 'AQD'],
            ['name' => 'তাফসীর', 'name_en' => 'Tafseer', 'code' => 'TAF'],
            ['name' => 'উসূলুল ফিকহ', 'name_en' => 'Usul al-Fiqh', 'code' => 'USF'],
            ['name' => 'উসূলুল হাদিস', 'name_en' => 'Usul al-Hadith', 'code' => 'USH'],
            ['name' => 'বালাগাত', 'name_en' => 'Balaghat', 'code' => 'BAL'],
            ['name' => 'মানতিক', 'name_en' => 'Mantiq', 'code' => 'MAN'],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['code' => $subject['code']], $subject);
        }

        // Shifts
        Shift::firstOrCreate(
            ['name' => 'প্রভাতি'],
            ['start_time' => '08:00', 'end_time' => '13:00']
        );
        Shift::firstOrCreate(
            ['name' => 'দিবা'],
            ['start_time' => '14:00', 'end_time' => '18:00']
        );

        // Grades
        $grades = [
            ['name' => 'A+', 'min_marks' => 80, 'max_marks' => 100, 'grade_point' => 5.00, 'remarks' => 'অত্যুত্তম'],
            ['name' => 'A', 'min_marks' => 70, 'max_marks' => 79, 'grade_point' => 4.00, 'remarks' => 'উত্তম'],
            ['name' => 'A-', 'min_marks' => 60, 'max_marks' => 69, 'grade_point' => 3.50, 'remarks' => 'ভালো'],
            ['name' => 'B', 'min_marks' => 50, 'max_marks' => 59, 'grade_point' => 3.00, 'remarks' => 'সন্তোষজনক'],
            ['name' => 'C', 'min_marks' => 40, 'max_marks' => 49, 'grade_point' => 2.00, 'remarks' => 'গ্রহণযোগ্য'],
            ['name' => 'D', 'min_marks' => 33, 'max_marks' => 39, 'grade_point' => 1.00, 'remarks' => 'পাস'],
            ['name' => 'F', 'min_marks' => 0, 'max_marks' => 32, 'grade_point' => 0.00, 'remarks' => 'ফেল'],
        ];

        foreach ($grades as $grade) {
            \DB::table('grades')->updateOrInsert(
                ['name' => $grade['name']],
                $grade
            );
        }

        // Fee Types
        $feeTypes = [
            ['name' => 'ভর্তি ফি', 'code' => 'ADM', 'is_recurring' => false],
            ['name' => 'মাসিক বেতন', 'code' => 'MON', 'is_recurring' => true],
            ['name' => 'পরীক্ষা ফি', 'code' => 'EXM', 'is_recurring' => false],
            ['name' => 'উন্নয়ন ফি', 'code' => 'DEV', 'is_recurring' => false],
            ['name' => 'হোস্টেল ফি', 'code' => 'HOS', 'is_recurring' => true],
            ['name' => 'পরিবহন ফি', 'code' => 'TRN', 'is_recurring' => true],
            ['name' => 'লাইব্রেরি ফি', 'code' => 'LIB', 'is_recurring' => false],
        ];

        foreach ($feeTypes as $fee) {
            \DB::table('fee_types')->updateOrInsert(
                ['code' => $fee['code']],
                $fee
            );
        }

        // Leave Types
        $leaveTypes = [
            ['name' => 'অসুস্থতাজনিত ছুটি', 'days_allowed' => 15],
            ['name' => 'ব্যক্তিগত ছুটি', 'days_allowed' => 10],
            ['name' => 'বার্ষিক ছুটি', 'days_allowed' => 20],
            ['name' => 'মাতৃত্বকালীন ছুটি', 'days_allowed' => 120],
        ];

        foreach ($leaveTypes as $leave) {
            \DB::table('leave_types')->updateOrInsert(
                ['name' => $leave['name']],
                $leave
            );
        }

        // Book Categories
        $bookCategories = [
            ['name' => 'কুরআন', 'name_en' => 'Quran'],
            ['name' => 'হাদিস', 'name_en' => 'Hadith'],
            ['name' => 'ফিকহ', 'name_en' => 'Fiqh'],
            ['name' => 'তাফসীর', 'name_en' => 'Tafseer'],
            ['name' => 'আরবি সাহিত্য', 'name_en' => 'Arabic Literature'],
            ['name' => 'ইসলামি ইতিহাস', 'name_en' => 'Islamic History'],
            ['name' => 'সীরাত', 'name_en' => 'Sirah'],
            ['name' => 'সাধারণ জ্ঞান', 'name_en' => 'General Knowledge'],
        ];

        foreach ($bookCategories as $cat) {
            \DB::table('book_categories')->updateOrInsert(
                ['name' => $cat['name']],
                $cat
            );
        }

        // Exam Types
        $examTypes = [
            ['name' => 'প্রথম সাময়িক', 'name_en' => 'First Term', 'percentage' => 25, 'order' => 1],
            ['name' => 'দ্বিতীয় সাময়িক', 'name_en' => 'Second Term', 'percentage' => 25, 'order' => 2],
            ['name' => 'বার্ষিক পরীক্ষা', 'name_en' => 'Annual Exam', 'percentage' => 50, 'order' => 3],
            ['name' => 'নির্বাচনী পরীক্ষা', 'name_en' => 'Selection Exam', 'percentage' => 100, 'order' => 4],
        ];

        foreach ($examTypes as $exam) {
            \DB::table('exam_types')->updateOrInsert(
                ['name' => $exam['name']],
                $exam
            );
        }

        // Income Heads
        $incomeHeads = [
            ['name' => 'ছাত্র বেতন', 'code' => 'STU_FEE'],
            ['name' => 'ভর্তি ফি', 'code' => 'ADM_FEE'],
            ['name' => 'সাধারণ দান', 'code' => 'DONATION'],
            ['name' => 'যাকাত', 'code' => 'ZAKAT'],
            ['name' => 'লিল্লাহ', 'code' => 'LILLAH'],
            ['name' => 'সরকারি অনুদান', 'code' => 'GOVT'],
            ['name' => 'অন্যান্য আয়', 'code' => 'OTHER'],
        ];

        foreach ($incomeHeads as $head) {
            \DB::table('income_heads')->updateOrInsert(
                ['code' => $head['code']],
                $head
            );
        }

        // Expense Heads
        $expenseHeads = [
            ['name' => 'বেতন ভাতা', 'code' => 'SALARY'],
            ['name' => 'বিদ্যুৎ বিল', 'code' => 'ELECTRIC'],
            ['name' => 'পানি বিল', 'code' => 'WATER'],
            ['name' => 'গ্যাস বিল', 'code' => 'GAS'],
            ['name' => 'রক্ষণাবেক্ষণ', 'code' => 'MAINTENANCE'],
            ['name' => 'স্টেশনারি', 'code' => 'STATIONERY'],
            ['name' => 'খাদ্য সামগ্রী', 'code' => 'FOOD'],
            ['name' => 'যাতায়াত', 'code' => 'TRANSPORT'],
            ['name' => 'অন্যান্য ব্যয়', 'code' => 'OTHER'],
        ];

        foreach ($expenseHeads as $head) {
            \DB::table('expense_heads')->updateOrInsert(
                ['code' => $head['code']],
                $head
            );
        }
    }

    private function createInitialSettings(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'institution_name', 'value' => 'মাদরাসা ম্যানেজমেন্ট', 'type' => 'text'],
            ['group' => 'general', 'key' => 'institution_name_en', 'value' => 'Madrasah Management', 'type' => 'text'],
            ['group' => 'general', 'key' => 'institution_address', 'value' => '', 'type' => 'textarea'],
            ['group' => 'general', 'key' => 'institution_phone', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'institution_email', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'institution_logo', 'value' => '', 'type' => 'file'],
            ['group' => 'general', 'key' => 'institution_favicon', 'value' => '', 'type' => 'file'],
            ['group' => 'academic', 'key' => 'admission_open', 'value' => 'true', 'type' => 'boolean'],
            ['group' => 'academic', 'key' => 'online_result', 'value' => 'true', 'type' => 'boolean'],
            ['group' => 'sms', 'key' => 'sms_gateway', 'value' => '', 'type' => 'text'],
            ['group' => 'sms', 'key' => 'sms_api_key', 'value' => '', 'type' => 'text'],
            ['group' => 'payment', 'key' => 'bkash_enabled', 'value' => 'false', 'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'nagad_enabled', 'value' => 'false', 'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'rocket_enabled', 'value' => 'false', 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            \DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
