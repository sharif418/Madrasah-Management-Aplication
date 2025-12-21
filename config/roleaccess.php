<?php

/**
 * Role-based Access Configuration
 * 
 * This file defines which Resources and Pages each role can access.
 * super_admin and principal have access to everything.
 * Other roles have limited access based on their responsibilities.
 * 
 * Names are class names without 'Resource' or 'Page' suffix.
 * e.g., 'Teacher' for TeacherResource, 'AttendanceReport' for AttendanceReport page
 */

return [
    /**
     * super_admin has access to EVERYTHING
     */
    'super_admin' => '*',

    /**
     * principal has access to EVERYTHING
     */
    'principal' => '*',

    /**
     * academic_admin - Manages academic affairs
     */
    'academic_admin' => [
        // Resources - Academic Setup
        'AcademicYear',
        'Department',
        'Designation',
        'ClassName',
        'Section',
        'Subject',
        'SubjectTeacher',
        'Shift',
        'Grade',

        // Resources - Students
        'Student',
        'StudentHealth',
        'Guardian',
        'AdmissionApplication',
        'Alumni',

        // Resources - Teachers & Staff
        'Teacher',
        'Staff',

        // Resources - Attendance
        'Attendance',
        'StaffAttendance',

        // Resources - Examination
        'Exam',
        'ExamSchedule',
        'Syllabus',
        'ClassRoutine',

        // Resources - Discipline
        'DisciplineIncident',

        // Resources - Communication
        'Notice',
        'Circular',
        'Event',
        'Download',

        // Pages
        'AttendanceReport',
        'MonthlyAttendanceSummary',
        'ClassPromotion',
        'StudentImport',
        'ProgressReport',
        'ReportCardGeneration',
        'AdmitCardReport',
        'SeatPlan',
        'SubjectAnalysis',
        'CertificateGeneration',
        'AttendanceCalendar',
    ],

    /**
     * accounts_admin - Manages finance and fees
     */
    'accounts_admin' => [
        // Resources - Fee Management
        'FeeType',
        'FeeStructure',
        'FeeCollection',
        'FeeDiscount',
        'FeeWaiver',
        'FeeRefund',
        'FeeInstallment',

        // Resources - Accounts
        'Income',
        'IncomeHead',
        'Expense',
        'ExpenseHead',
        'BankAccount',
        'Budget',
        'Donation',

        // Resources - Salary
        'SalaryPayment',
        'SalaryAdvance',
        'StaffLoan',

        // Resources - View Students (for fee collection)
        'Student',

        // Pages - Accounts
        'BulkFeeCollection',
        'DueReport',
        'FeeReminder',
        'FeeSummary',
        'CashBook',
        'Ledger',
        'BalanceSheet',
        'ProfitLossStatement',
    ],

    /**
     * teacher - Teaching staff with limited access
     */
    'teacher' => [
        // Resources
        'Attendance',
        'ClassRoutine',
        'HifzProgress',
        'HifzSummary',
        'KitabProgress',
        'Kitab',
        'LeaveApplication',
        'Notice',
        'Circular',
        'Event',
        'Syllabus',

        // Pages
        'HifzDailyEntry',
        'HifzProgressReport',
        'KitabProgressDashboard',
        'AttendanceReport',
    ],

    /**
     * librarian - Library management
     */
    'librarian' => [
        // Resources
        'Book',
        'BookCategory',
        'BookIssue',
        'LibraryMember',
        'Student',

        // Pages
        'LibraryCardPrint',
        'LibraryStockReport',
    ],

    /**
     * hostel_warden - Hostel management
     */
    'hostel_warden' => [
        // Resources
        'Hostel',
        'HostelRoom',
        'HostelAllocation',
        'HostelVisitor',
        'MealMenu',
        'MedicalVisit',
        'Student',
    ],

    /**
     * transport_manager - Transport management
     */
    'transport_manager' => [
        // Resources
        'Vehicle',
        'VehicleMaintenance',
        'TransportRoute',
        'TransportAllocation',
        'FuelLog',
    ],
];
