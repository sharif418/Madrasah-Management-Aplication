<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\Website\WebsiteController;
use App\Http\Controllers\Website\GalleryController;
use App\Http\Controllers\Website\NewsController;
use App\Http\Controllers\Website\EventController;
use App\Http\Controllers\Website\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========== PUBLIC WEBSITE ROUTES ==========
Route::get('/', [WebsiteController::class, 'home'])->name('home');

// About Pages
Route::prefix('about')->group(function () {
    Route::get('/', [WebsiteController::class, 'about'])->name('about');
    Route::get('/history', [WebsiteController::class, 'history'])->name('about.history');
    Route::get('/mission', [WebsiteController::class, 'mission'])->name('about.mission');
    Route::get('/committee', [WebsiteController::class, 'committee'])->name('about.committee');
    Route::get('/teachers', [WebsiteController::class, 'teachers'])->name('about.teachers');
    Route::get('/staff', [WebsiteController::class, 'staff'])->name('about.staff');
});

// Academic Pages
Route::get('/departments', [WebsiteController::class, 'departments'])->name('departments');
Route::get('/routine', [WebsiteController::class, 'routine'])->name('routine');
Route::get('/academic-calendar', [WebsiteController::class, 'calendar'])->name('academic.calendar');
Route::get('/results', [WebsiteController::class, 'results'])->name('results');

// Admission
// Admission
Route::prefix('admission')->group(function () {
    Route::get('/', [WebsiteController::class, 'admission'])->name('admission');
    Route::get('/apply', [WebsiteController::class, 'apply'])->name('admission.apply');
    Route::post('/apply', [WebsiteController::class, 'storeAdmission'])->name('admission.store');
    Route::get('/eligibility', [WebsiteController::class, 'eligibility'])->name('admission.eligibility');
    Route::get('/fees', [WebsiteController::class, 'fees'])->name('admission.fees');
});

// Gallery
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::get('/gallery/videos', [GalleryController::class, 'videos'])->name('gallery.videos');
Route::get('/gallery/{album}', [GalleryController::class, 'show'])->name('gallery.show');

// News
Route::get('/news', [NewsController::class, 'index'])->name('news');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Events
Route::get('/events', [EventController::class, 'index'])->name('events');

// Resources
Route::get('/downloads', [WebsiteController::class, 'downloads'])->name('downloads');
Route::get('/faq', [WebsiteController::class, 'faq'])->name('faq');
Route::get('/circulars', [WebsiteController::class, 'circulars'])->name('circulars');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Donate
Route::get('/donate', [WebsiteController::class, 'donate'])->name('donate');

// Portal Links
Route::get('/portal', [WebsiteController::class, 'portal'])->name('portal');

// User Guide
Route::get('/user-guide', [WebsiteController::class, 'userGuide'])->name('user-guide');


// Student Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/{student}/id-card', [StudentController::class, 'idCard'])->name('id-card');
    Route::get('/{student}/tc', [StudentController::class, 'transferCertificate'])->name('tc');
});

// Teacher Routes
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/{teacher}/id-card', [TeacherController::class, 'idCard'])->name('id-card');
});

// Export Routes
Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
Route::get('/teachers/export', [TeacherController::class, 'export'])->name('teachers.export');

// Exam Routes
Route::get('/exam/{exam}/tabulation', [\App\Http\Controllers\ExamController::class, 'tabulation'])->name('exam.tabulation');
Route::get('/exam/{exam}/marksheet/{student}', [\App\Http\Controllers\ExamController::class, 'marksheet'])->name('exam.marksheet');

// Fee Routes
Route::prefix('fee')->name('fee.')->group(function () {
    Route::get('/receipt/{payment}', [\App\Http\Controllers\FeeController::class, 'receipt'])->name('receipt');
    Route::get('/due-report', [\App\Http\Controllers\FeeController::class, 'dueReport'])->name('due-report');
    Route::get('/collection-report', [\App\Http\Controllers\FeeController::class, 'collectionReport'])->name('collection-report');
});

// Donation Routes
Route::get('/donation/receipt/{donation}', [\App\Http\Controllers\AccountController::class, 'donationReceipt'])->name('donation.receipt');
