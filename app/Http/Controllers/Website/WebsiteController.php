<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\News;
use App\Models\Event;
use App\Models\Testimonial;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Student;
use App\Models\ClassName;
use App\Models\GalleryAlbum;
use App\Models\Faq;
use App\Models\Download;
use App\Models\Circular;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WebsiteController extends Controller
{
    public function home()
    {
        $sliders = Slider::where('is_active', true)->orderBy('order')->get();
        $news = News::where('is_published', true)->latest()->take(6)->get();
        $events = Event::where('is_public', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(4)
            ->get();
        $testimonials = Testimonial::where('is_published', true)->inRandomOrder()->take(6)->get();

        // Live stats
        $stats = [
            'students' => Student::where('status', 'active')->count(),
            'teachers' => Teacher::where('status', 'active')->count(),
            'staff' => Staff::where('status', 'active')->count(),
            'classes' => ClassName::count(),
        ];

        $prayerData = $this->getPrayerTimes();

        return view('website.home', compact('sliders', 'news', 'events', 'testimonials', 'stats', 'prayerData'));
    }

    private function getPrayerTimes()
    {
        return Cache::remember('prayer_times_' . date('Y-m-d'), 60 * 60 * 24, function () {
            try {
                // Dhaka Coordinates
                $lat = 23.8103;
                $lng = 90.4125;
                $date = now();

                $response = Http::withoutVerifying()->timeout(10)->get("https://api.aladhan.com/v1/timings/" . $date->format('d-m-Y'), [
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'method' => 1,
                    'school' => 1,
                ]);

                if ($response->successful()) {
                    return $response->json()['data'];
                }
            } catch (\Exception $e) {
                // Log error if needed
            }

            // Fallback Data (Approximate Fixed Times)
            return [
                'timings' => [
                    'Fajr' => '05:15',
                    'Dhuhr' => '12:05',
                    'Asr' => '15:45',
                    'Maghrib' => '17:18',
                    'Isha' => '18:40',
                ],
                'date' => [
                    'hijri' => [
                        'day' => now()->format('d'), // Fallback to gregorian day if failed
                        'month' => ['en' => now()->format('F'), 'bn' => now()->translatedFormat('F')],
                        'year' => now()->format('Y'),
                    ]
                ]
            ];
        });
    }

    public function about()
    {
        return view('website.about.index');
    }

    public function history()
    {
        return view('website.about.history');
    }

    public function mission()
    {
        return view('website.about.mission');
    }

    public function committee()
    {
        return view('website.about.committee');
    }

    public function teachers()
    {
        $teachers = Teacher::where('status', 'active')
            ->with('designation')
            ->get()
            ->groupBy(fn($t) => $t->designation?->name ?? 'অন্যান্য');

        return view('website.about.teachers', compact('teachers'));
    }

    public function staff()
    {
        $staff = Staff::where('status', 'active')
            ->with('designation')
            ->get();

        return view('website.about.staff', compact('staff'));
    }

    public function departments()
    {
        $departments = ClassName::withCount(['students' => fn($q) => $q->where('status', 'active')])
            ->get();

        return view('website.academic.departments', compact('departments'));
    }

    public function downloads()
    {
        $downloads = Download::where('is_published', true)
            ->orderBy('category')
            ->get()
            ->groupBy('category');

        return view('website.downloads', compact('downloads'));
    }

    public function faq()
    {
        $faqs = Faq::where('is_published', true)
            ->orderBy('order')
            ->get()
            ->groupBy('category');

        return view('website.faq', compact('faqs'));
    }

    public function donate()
    {
        return view('website.donate');
    }

    public function admission()
    {
        return view('website.admission.index');
    }

    public function portal()
    {
        return view('website.portal');
    }

    public function circulars()
    {
        $circulars = Circular::where('status', 'published')
            ->latest('issue_date')
            ->paginate(20);

        return view('website.circulars', compact('circulars'));
    }

    public function routine()
    {
        $classes = \App\Models\ClassName::where('is_active', true)->orderBy('order')->get();
        $routines = \App\Models\ClassRoutine::with(['subject', 'teacher'])
            ->whereHas('academicYear', function ($q) {
                $q->where('is_current', true);
            })
            ->orderBy('start_time')
            ->get()
            ->groupBy(['class_id', 'day']);

        return view('website.academic.routine', compact('classes', 'routines'));
    }

    public function calendar()
    {
        $currentYear = \App\Models\AcademicYear::current();
        $events = collect();

        if ($currentYear) {
            $events = \App\Models\Event::whereBetween('start_date', [$currentYear->start_date, $currentYear->end_date])
                ->orderBy('start_date')
                ->get()
                ->groupBy(function ($event) {
                    return $event->start_date->format('F Y');
                });
        }

        return view('website.academic.calendar', compact('events'));
    }

    public function results()
    {
        $exams = \App\Models\Exam::with('academicYear')
            ->latest('start_date')
            ->take(10)
            ->get();

        return view('website.academic.results', compact('exams'));
    }

    public function searchResult(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'roll' => 'required',
            'exam_id' => 'required|exists:exams,id',
        ]);

        $student = \App\Models\Student::where('roll_no', $request->roll)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'এই রোল নম্বরের কোনো ছাত্র পাওয়া যায়নি।'], 404);
        }

        $summary = \App\Models\ExamResult::where('exam_id', $request->exam_id)
            ->where('student_id', $student->id)
            ->with(['student.class', 'exam'])
            ->first();

        if (!$summary) {
            return response()->json(['success' => false, 'message' => 'এই পরীক্ষার ফলাফল এখনও প্রকাশিত হয়নি বা খুঁজে পাওয়া যায়নি।'], 404);
        }

        $marks = \App\Models\Mark::where('exam_id', $request->exam_id)
            ->where('student_id', $student->id)
            ->with('subject')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student,
                'result' => $summary,
                'marks' => $marks
            ]
        ]);
    }

    public function apply()
    {
        $classes = ClassName::where('is_active', true)->orderBy('order')->get();
        return view('website.admission.apply', compact('classes'));
    }

    public function storeAdmission(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'student_name_en' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'blood_group' => 'nullable|string',
            'father_name' => 'required|string|max:255',
            'father_phone' => 'required|string|max:20',
            'mother_name' => 'required|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'present_address' => 'required|string',
            'permanent_address' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'previous_institution' => 'nullable|string|max:255',
        ]);

        $currentYear = \App\Models\AcademicYear::current();

        if (!$currentYear) {
            // Fallback: Try getting the latest one if no current year is set
            $currentYear = \App\Models\AcademicYear::latest('start_date')->first();

            if (!$currentYear) {
                return response()->json(['success' => false, 'message' => 'ভর্তির জন্য কোন শিক্ষাবর্ষ চালু নেই।'], 422);
            }
        }

        try {
            $application = \App\Models\AdmissionApplication::create([
                'application_no' => \App\Models\AdmissionApplication::generateApplicationNo(),
                'academic_year_id' => $currentYear->id,
                'class_id' => $validated['class_id'],
                'student_name' => $validated['student_name'],
                'student_name_en' => $validated['student_name_en'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'blood_group' => $validated['blood_group'],
                'father_name' => $validated['father_name'],
                'father_phone' => $validated['father_phone'],
                'father_occupation' => $request->father_occupation,
                'mother_name' => $validated['mother_name'],
                'mother_phone' => $validated['mother_phone'],
                'present_address' => $validated['present_address'],
                'permanent_address' => $validated['permanent_address'],
                'previous_school' => $validated['previous_institution'], // Mapped
                'previous_class' => $request->previous_class,
                'status' => \App\Models\AdmissionApplication::STATUS_PENDING,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'আবেদন সফলভাবে ঘ্রহণ করা হয়েছে!',
                'application_no' => $application->application_no
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e);
            return response()->json(['success' => false, 'message' => 'দুঃখিত, আবেদন জমা দেওয়া সম্ভব হয়নি। আবার চেষ্টা করুন।'], 500);
        }
    }

    public function eligibility()
    {
        return view('website.admission.eligibility');
    }

    public function fees()
    {
        return view('website.admission.fees');
    }
}
