<?php

/**
 * Role-based Resource Access Configuration
 * 
 * This file defines which resources each role can access.
 * super_admin and principal have access to everything.
 * Other roles have limited access based on their responsibilities.
 * 
 * Resource names are the class names without 'Resource' suffix.
 * e.g., 'Teacher' for TeacherResource
 */

return [
    /**
     * super_admin has access to EVERYTHING
     * No need to list resources - handled in BaseResource
     */
    'super_admin' => '*',

    /**
     * principal has access to EVERYTHING except system settings
     */
    'principal' => '*',

    /**
     * academic_admin - Manages academic affairs
     */
    'academic_admin' => [
        // Academic Setup
        'AcademicYear',
        'Department',
        'Designation',
        'ClassName',
        'Section',
        'Subject',
        'SubjectTeacher',
        'Shift',
        'Grade',

        // Students
        'Student',
        'StudentHealth',
        'Guardian',
        'Admission', // AdmissionApplication
        'Alumni',

        // Teachers & Staff
        'Teacher',
        'Staff',

        // Attendance
        'Attendance',
        'StaffAttendance',

        // Examination
        'Exam',
        'ExamSchedule',
        'Syllabus',
        'ClassRoutine',

        // Discipline
        'DisciplineIncident',

        // Notices & Communication
        'Notice',
        'Circular',
        'Event',
        'Download',
    ],

    /**
     * accounts_admin - Manages finance and fees
     */
    'accounts_admin' => [
        // Fee Management
        'FeeType',
        'FeeStructure',
        'FeeCollection', // StudentFee
        'FeeDiscount',
        'FeeWaiver',
        'FeeRefund',
        'FeeInstallment',

        // Accounts
        'Income',
        'IncomeHead',
        'Expense',
        'ExpenseHead',
        'BankAccount',
        'Budget',
        'Donation',

        // Salary
        'SalaryPayment',
        'SalaryAdvance',
        'StaffLoan',

        // View Students (for fee collection)
        'Student',
    ],

    /**
     * teacher - Teaching staff with limited access
     */
    'teacher' => [
        // Attendance (can take for their classes)
        'Attendance',

        // Class Routine (view)
        'ClassRoutine',

        // Hifz Module (entry)
        'HifzProgress',
        'HifzSummary',
        'KitabProgress',
        'Kitab',

        // Leave Applications (own)
        'LeaveApplication',

        // View Only
        'Notice',
        'Circular',
        'Event',
        'Syllabus',

        // Subject & Class (view)
        'Subject',
        'ClassName',
        'Section',
    ],

    /**
     * librarian - Library management
     */
    'librarian' => [
        'Book',
        'BookCategory',
        'BookIssue',
        'LibraryMember',

        // View students for issuing
        'Student',
    ],

    /**
     * hostel_warden - Hostel management
     */
    'hostel_warden' => [
        'Hostel',
        'HostelRoom',
        'HostelAllocation',
        'HostelVisitor',
        'MealMenu',
        'MedicalVisit',

        // View students
        'Student',
    ],

    /**
     * transport_manager - Transport management (optional role)
     */
    'transport_manager' => [
        'Vehicle',
        'VehicleMaintenance',
        'TransportRoute',
        'TransportAllocation',
        'FuelLog',
    ],
];
