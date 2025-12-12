<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * এই seeder সব role এর জন্য permissions assign করবে
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permission groups
        $permissionGroups = $this->getPermissionGroups();

        // Create all permissions
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            }
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        // Give super_admin all permissions
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Permission::all());
        }
    }

    private function getPermissionGroups(): array
    {
        return [
            // Student Management
            'students' => [
                'view_any_student',
                'view_student',
                'create_student',
                'update_student',
                'delete_student',
                'delete_any_student',
                'restore_student',
                'force_delete_student',
            ],

            // Teacher/Staff Management
            'teachers' => [
                'view_any_teacher',
                'view_teacher',
                'create_teacher',
                'update_teacher',
                'delete_teacher',
                'delete_any_teacher',
            ],

            'staff' => [
                'view_any_staff',
                'view_staff',
                'create_staff',
                'update_staff',
                'delete_staff',
            ],

            // Academic
            'classes' => [
                'view_any_class',
                'view_class',
                'create_class',
                'update_class',
                'delete_class',
            ],

            'sections' => [
                'view_any_section',
                'view_section',
                'create_section',
                'update_section',
                'delete_section',
            ],

            'subjects' => [
                'view_any_subject',
                'view_subject',
                'create_subject',
                'update_subject',
                'delete_subject',
            ],

            // Attendance
            'attendances' => [
                'view_any_attendance',
                'view_attendance',
                'create_attendance',
                'update_attendance',
                'delete_attendance',
                'bulk_attendance',
            ],

            'staff_attendances' => [
                'view_any_staff_attendance',
                'view_staff_attendance',
                'create_staff_attendance',
                'update_staff_attendance',
            ],

            // Exams
            'exams' => [
                'view_any_exam',
                'view_exam',
                'create_exam',
                'update_exam',
                'delete_exam',
            ],

            'exam_results' => [
                'view_any_exam_result',
                'view_exam_result',
                'create_exam_result',
                'update_exam_result',
                'delete_exam_result',
                'publish_result',
            ],

            'marks' => [
                'view_any_mark',
                'view_mark',
                'create_mark',
                'update_mark',
                'entry_marks',
            ],

            // Fees
            'fee_types' => [
                'view_any_fee_type',
                'view_fee_type',
                'create_fee_type',
                'update_fee_type',
                'delete_fee_type',
            ],

            'fee_collections' => [
                'view_any_fee_collection',
                'view_fee_collection',
                'create_fee_collection',
                'update_fee_collection',
                'delete_fee_collection',
                'collect_fee',
            ],

            'student_fees' => [
                'view_any_student_fee',
                'view_student_fee',
                'create_student_fee',
                'update_student_fee',
                'assign_fee',
            ],

            // Accounts
            'incomes' => [
                'view_any_income',
                'view_income',
                'create_income',
                'update_income',
                'delete_income',
            ],

            'expenses' => [
                'view_any_expense',
                'view_expense',
                'create_expense',
                'update_expense',
                'delete_expense',
            ],

            'salaries' => [
                'view_any_salary',
                'view_salary',
                'create_salary',
                'update_salary',
                'pay_salary',
            ],

            // Library
            'books' => [
                'view_any_book',
                'view_book',
                'create_book',
                'update_book',
                'delete_book',
            ],

            'book_issues' => [
                'view_any_book_issue',
                'view_book_issue',
                'create_book_issue',
                'update_book_issue',
                'return_book',
            ],

            // Hostel
            'hostels' => [
                'view_any_hostel',
                'view_hostel',
                'create_hostel',
                'update_hostel',
                'delete_hostel',
            ],

            'hostel_allocations' => [
                'view_any_hostel_allocation',
                'view_hostel_allocation',
                'create_hostel_allocation',
                'update_hostel_allocation',
            ],

            // Transport
            'vehicles' => [
                'view_any_vehicle',
                'view_vehicle',
                'create_vehicle',
                'update_vehicle',
                'delete_vehicle',
            ],

            'transport_allocations' => [
                'view_any_transport_allocation',
                'view_transport_allocation',
                'create_transport_allocation',
                'update_transport_allocation',
            ],

            // Communication
            'notices' => [
                'view_any_notice',
                'view_notice',
                'create_notice',
                'update_notice',
                'delete_notice',
                'publish_notice',
            ],

            'sms' => [
                'send_sms',
                'view_sms_log',
                'bulk_sms',
            ],

            // Website
            'website' => [
                'manage_sliders',
                'manage_galleries',
                'manage_news',
                'manage_faqs',
                'manage_downloads',
                'manage_testimonials',
            ],

            // Settings
            'settings' => [
                'view_settings',
                'update_settings',
                'manage_users',
                'manage_roles',
            ],

            // Reports
            'reports' => [
                'view_student_reports',
                'view_staff_reports',
                'view_financial_reports',
                'view_academic_reports',
                'export_reports',
            ],

            // Hifz & Kitab
            'hifz' => [
                'view_any_hifz_progress',
                'view_hifz_progress',
                'create_hifz_progress',
                'update_hifz_progress',
            ],

            'kitab' => [
                'view_any_kitab_progress',
                'view_kitab_progress',
                'create_kitab_progress',
                'update_kitab_progress',
            ],
        ];
    }

    private function assignPermissionsToRoles(): void
    {
        // Principal - can view everything but limited edit
        $principal = Role::where('name', 'principal')->first();
        if ($principal) {
            $principal->givePermissionTo([
                // View permissions
                'view_any_student',
                'view_student',
                'view_any_teacher',
                'view_teacher',
                'view_any_staff',
                'view_staff',
                'view_any_class',
                'view_class',
                'view_any_attendance',
                'view_attendance',
                'view_any_exam',
                'view_exam',
                'view_any_exam_result',
                'view_exam_result',
                'publish_result',
                'view_any_fee_collection',
                'view_fee_collection',
                'view_any_income',
                'view_income',
                'view_any_expense',
                'view_expense',
                'view_any_notice',
                'view_notice',
                'create_notice',
                'publish_notice',
                'view_settings',
                'view_student_reports',
                'view_staff_reports',
                'view_financial_reports',
                'view_academic_reports',
            ]);
        }

        // Academic Admin
        $academicAdmin = Role::where('name', 'academic_admin')->first();
        if ($academicAdmin) {
            $academicAdmin->givePermissionTo([
                // Student management
                'view_any_student',
                'view_student',
                'create_student',
                'update_student',
                'delete_student',
                // Teacher view
                'view_any_teacher',
                'view_teacher',
                // Academic setup
                'view_any_class',
                'view_class',
                'create_class',
                'update_class',
                'delete_class',
                'view_any_section',
                'view_section',
                'create_section',
                'update_section',
                'delete_section',
                'view_any_subject',
                'view_subject',
                'create_subject',
                'update_subject',
                'delete_subject',
                // Attendance
                'view_any_attendance',
                'view_attendance',
                'create_attendance',
                'update_attendance',
                'bulk_attendance',
                // Exams
                'view_any_exam',
                'view_exam',
                'create_exam',
                'update_exam',
                'delete_exam',
                'view_any_exam_result',
                'view_exam_result',
                'create_exam_result',
                'update_exam_result',
                'publish_result',
                'view_any_mark',
                'view_mark',
                'create_mark',
                'update_mark',
                'entry_marks',
                // Notices
                'view_any_notice',
                'view_notice',
                'create_notice',
                'update_notice',
                // Reports
                'view_student_reports',
                'view_academic_reports',
                'export_reports',
                // Hifz
                'view_any_hifz_progress',
                'view_hifz_progress',
                'create_hifz_progress',
                'update_hifz_progress',
                'view_any_kitab_progress',
                'view_kitab_progress',
                'create_kitab_progress',
                'update_kitab_progress',
            ]);
        }

        // Accounts Admin
        $accountsAdmin = Role::where('name', 'accounts_admin')->first();
        if ($accountsAdmin) {
            $accountsAdmin->givePermissionTo([
                // View students
                'view_any_student',
                'view_student',
                // Fee management
                'view_any_fee_type',
                'view_fee_type',
                'create_fee_type',
                'update_fee_type',
                'view_any_fee_collection',
                'view_fee_collection',
                'create_fee_collection',
                'update_fee_collection',
                'collect_fee',
                'view_any_student_fee',
                'view_student_fee',
                'create_student_fee',
                'update_student_fee',
                'assign_fee',
                // Accounts
                'view_any_income',
                'view_income',
                'create_income',
                'update_income',
                'delete_income',
                'view_any_expense',
                'view_expense',
                'create_expense',
                'update_expense',
                'delete_expense',
                'view_any_salary',
                'view_salary',
                'create_salary',
                'update_salary',
                'pay_salary',
                // Reports
                'view_financial_reports',
                'export_reports',
            ]);
        }

        // Teacher
        $teacher = Role::where('name', 'teacher')->first();
        if ($teacher) {
            $teacher->givePermissionTo([
                // Students
                'view_any_student',
                'view_student',
                // Attendance
                'view_any_attendance',
                'view_attendance',
                'create_attendance',
                'update_attendance',
                'bulk_attendance',
                // Marks
                'view_any_mark',
                'view_mark',
                'create_mark',
                'update_mark',
                'entry_marks',
                // Notices
                'view_any_notice',
                'view_notice',
                // Hifz
                'view_any_hifz_progress',
                'view_hifz_progress',
                'create_hifz_progress',
                'update_hifz_progress',
                'view_any_kitab_progress',
                'view_kitab_progress',
                'create_kitab_progress',
                'update_kitab_progress',
            ]);
        }

        // Librarian
        $librarian = Role::where('name', 'librarian')->first();
        if ($librarian) {
            $librarian->givePermissionTo([
                // Students view
                'view_any_student',
                'view_student',
                // Library
                'view_any_book',
                'view_book',
                'create_book',
                'update_book',
                'delete_book',
                'view_any_book_issue',
                'view_book_issue',
                'create_book_issue',
                'update_book_issue',
                'return_book',
            ]);
        }

        // Hostel Warden
        $hostelWarden = Role::where('name', 'hostel_warden')->first();
        if ($hostelWarden) {
            $hostelWarden->givePermissionTo([
                // Students view
                'view_any_student',
                'view_student',
                // Hostel
                'view_any_hostel',
                'view_hostel',
                'create_hostel',
                'update_hostel',
                'view_any_hostel_allocation',
                'view_hostel_allocation',
                'create_hostel_allocation',
                'update_hostel_allocation',
            ]);
        }
    }
}
